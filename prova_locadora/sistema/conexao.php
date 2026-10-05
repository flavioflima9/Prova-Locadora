<?php

    // Dados de acesso ao banco (padrão do XAMPP)
    $servidor = "localhost";
    $usuario = "root";
    $senha = "";
    $banco = "locadora_db";

    // Chave usada para criptografar CPF e CNH dos clientes (AES).
    // Precisa ser a mesma usada no script locadora_db.sql.
    define('CHAVE_CRIPTO', 'locadora@2026#chave-secreta');

    // Tempo máximo sem usar o sistema antes de a sessão expirar (30 minutos)
    define('TEMPO_SESSAO', 1800);

    // Faz o mysqli lançar exceções em caso de erro (já é o padrão no PHP 8.1+)
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli($servidor, $usuario, $senha, $banco);
        $conn->set_charset("utf8mb4");
    } catch (mysqli_sql_exception $e) {
        die("Não foi possível conectar ao banco de dados. Verifique se o MySQL está ligado e se o arquivo locadora_db.sql foi importado.");
    }

?>
