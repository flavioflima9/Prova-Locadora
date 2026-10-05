-- =====================================================================
-- locadora_db.sql
-- Sistema de Locação de Veículos
-- SGBD: MySQL / MariaDB (XAMPP)
--
-- Como usar: importe este arquivo no phpMyAdmin (aba "Importar") ou rode
--   mysql -u root < locadora_db.sql
--
-- Logins de teste (senha em texto só aqui; no banco fica o hash bcrypt):
--   admin@locadora.com  / admin123
--   marina@locadora.com / marina123
--   carlos@locadora.com / carlos123
-- =====================================================================

DROP DATABASE IF EXISTS locadora_db;
CREATE DATABASE locadora_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE locadora_db;

-- ---------------------------------------------------------------------
-- Usuários do sistema (quem faz login). A senha guarda o hash bcrypt
-- gerado por password_hash() do PHP, nunca a senha em texto puro.
-- ---------------------------------------------------------------------
CREATE TABLE usuario (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(100) NOT NULL,
    email     VARCHAR(120) NOT NULL,
    senha     VARCHAR(255) NOT NULL,
    criado_em DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuario_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Clientes. CPF e CNH são dados sensíveis: ficam criptografados (AES)
-- em colunas VARBINARY. O sistema usa AES_ENCRYPT/AES_DECRYPT.
-- ---------------------------------------------------------------------
CREATE TABLE cliente (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome            VARCHAR(100) NOT NULL,
    email           VARCHAR(120) NOT NULL,
    telefone        VARCHAR(20)  NOT NULL,
    cpf             VARBINARY(64) NOT NULL,
    cnh             VARBINARY(64) NOT NULL,
    data_nascimento DATE         NOT NULL,
    criado_em       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cliente_email (email),
    UNIQUE KEY uq_cliente_cpf (cpf)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Veículos
-- ---------------------------------------------------------------------
CREATE TABLE veiculo (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    placa        VARCHAR(7)   NOT NULL,
    marca        VARCHAR(50)  NOT NULL,
    modelo       VARCHAR(60)  NOT NULL,
    ano          SMALLINT UNSIGNED NOT NULL,
    cor          VARCHAR(30)  NOT NULL,
    valor_diaria DECIMAL(10,2) NOT NULL,
    status       ENUM('disponivel','alugado','manutencao') NOT NULL DEFAULT 'disponivel',
    PRIMARY KEY (id),
    UNIQUE KEY uq_veiculo_placa (placa),
    CONSTRAINT ck_veiculo_diaria CHECK (valor_diaria > 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Locações: ligam um cliente a um veículo e registram o usuário que
-- cadastrou a locação. As chaves estrangeiras impedem apagar um cliente,
-- veículo ou usuário que já tenha locações.
-- ---------------------------------------------------------------------
CREATE TABLE locacao (
    id                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    cliente_id              INT UNSIGNED NOT NULL,
    veiculo_id              INT UNSIGNED NOT NULL,
    usuario_id              INT UNSIGNED NOT NULL,
    data_retirada           DATE NOT NULL,
    data_prevista_devolucao DATE NOT NULL,
    data_devolucao          DATE NULL,
    valor_total             DECIMAL(10,2) NOT NULL,
    status                  ENUM('ativa','finalizada') NOT NULL DEFAULT 'ativa',
    PRIMARY KEY (id),
    KEY idx_locacao_data_retirada (data_retirada),
    CONSTRAINT fk_locacao_cliente FOREIGN KEY (cliente_id)
        REFERENCES cliente (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_locacao_veiculo FOREIGN KEY (veiculo_id)
        REFERENCES veiculo (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_locacao_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT ck_locacao_datas CHECK (data_prevista_devolucao >= data_retirada)
) ENGINE=InnoDB;

-- =====================================================================
-- DADOS DE EXEMPLO
-- =====================================================================

-- Mesma chave definida em sistema/conexao.php (CHAVE_CRIPTO)
SET @chave = 'locadora@2026#chave-secreta';

INSERT INTO usuario (nome, email, senha) VALUES
('Administrador', 'admin@locadora.com',  '$2y$10$GFaQQwFXTNZuL3Js0CWnSeda.d.YpBGcyLcDJ2dpoWwzU.kT3VGwq'),
('Marina Souza',  'marina@locadora.com', '$2y$10$fef2FN39RfzzbMDrPNm1Gex3LMWaADNMqFwiv0zHvNJsRf3bgrlz.'),
('Carlos Lima',   'carlos@locadora.com', '$2y$10$fPawpYT99qVH3G0NkMp7P.NEachg5fsnlUh7BF6npkqIXYbLa.7S.');

INSERT INTO cliente (nome, email, telefone, cpf, cnh, data_nascimento) VALUES
('Ana Beatriz Lima',    'ana.lima@example.com',     '(81) 99871-2043', AES_ENCRYPT('52998224725', @chave), AES_ENCRYPT('04821937560', @chave), '1992-03-14'),
('Rafael Moura',        'rafael.moura@example.com', '(81) 98812-7765', AES_ENCRYPT('52601815906', @chave), AES_ENCRYPT('07315928846', @chave), '1988-11-02'),
('Juliana Prado',       'juliana.prado@example.com','(81) 99654-3120', AES_ENCRYPT('08301661305', @chave), AES_ENCRYPT('05692017733', @chave), '1995-07-21'),
('Pedro Henrique Alves','pedro.alves@example.com',  '(81) 98743-9988', AES_ENCRYPT('18609139034', @chave), AES_ENCRYPT('03987451268', @chave), '1990-01-30');

INSERT INTO veiculo (placa, marca, modelo, ano, cor, valor_diaria, status) VALUES
('ABC1D23', 'Fiat',      'Argo',     2023, 'Branco',   119.90, 'disponivel'),
('BRA2E19', 'Volkswagen','Polo',     2022, 'Prata',    129.90, 'disponivel'),
('QWE3R45', 'Chevrolet', 'Onix',     2024, 'Preto',    109.90, 'alugado'),
('XYZ4F56', 'Toyota',    'Corolla',  2023, 'Cinza',    219.90, 'alugado'),
('JKL5G67', 'Jeep',      'Renegade', 2022, 'Vermelho', 189.90, 'manutencao');

-- Duas locações finalizadas (histórico) e duas em andamento
INSERT INTO locacao
    (cliente_id, veiculo_id, usuario_id, data_retirada, data_prevista_devolucao, data_devolucao, valor_total, status)
VALUES
(1, 1, 2, '2026-09-20', '2026-09-25', '2026-09-25',  599.50, 'finalizada'),
(2, 4, 2, '2026-09-28', '2026-10-05', NULL,         1539.30, 'ativa'),
(3, 2, 3, '2026-09-10', '2026-09-15', '2026-09-16',  779.40, 'finalizada'),
(4, 3, 1, '2026-09-30', '2026-10-03', NULL,          329.70, 'ativa');
