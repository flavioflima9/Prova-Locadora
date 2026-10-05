<?php

// Escapa texto antes de mostrar na tela (evita XSS)
function h($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

function soDigitos($texto) {
    return preg_replace('/\D/', '', (string)$texto);
}

// Confere os dois dígitos verificadores do CPF
function cpfValido($cpf) {
    $cpf = soDigitos($cpf);
    if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ($cpf[$t] != $digito) {
            return false;
        }
    }
    return true;
}

function formatarCpf($cpf) {
    $c = soDigitos($cpf);
    if (strlen($c) != 11) {
        return $c;
    }
    return substr($c, 0, 3) . '.' . substr($c, 3, 3) . '.' . substr($c, 6, 3) . '-' . substr($c, 9, 2);
}

// Na listagem o CPF aparece parcialmente oculto: ***.456.789-**
function mascararCpf($cpf) {
    $c = soDigitos($cpf);
    if (strlen($c) != 11) {
        return '***';
    }
    return '***.' . substr($c, 3, 3) . '.' . substr($c, 6, 3) . '-**';
}

function formatarData($data) {
    return $data ? date('d/m/Y', strtotime($data)) : '-';
}

function dinheiro($valor) {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

// Aceita só datas reais no formato AAAA-MM-DD
function dataValida($data) {
    $d = DateTime::createFromFormat('Y-m-d', (string)$data);
    return $d && $d->format('Y-m-d') === $data;
}

function seloStatusVeiculo($status) {
    $mapa = [
        'disponivel' => ['verde', 'Disponível'],
        'alugado'    => ['azul', 'Alugado'],
        'manutencao' => ['amarelo', 'Em manutenção'],
    ];
    $s = $mapa[$status] ?? ['amarelo', $status];
    return '<span class="selo ' . $s[0] . '">' . h($s[1]) . '</span>';
}

// Mostra a mensagem indicada em ?msg=codigo (só códigos conhecidos)
function mensagemFlash() {
    $mensagens = [
        'cadastrado'     => ['ok',   'Registro cadastrado com sucesso.'],
        'atualizado'     => ['ok',   'Registro atualizado com sucesso.'],
        'excluido'       => ['ok',   'Registro excluído com sucesso.'],
        'devolvido'      => ['ok',   'Devolução registrada. O veículo voltou a ficar disponível.'],
        'cancelada'      => ['ok',   'Locação cancelada. O veículo foi liberado.'],
        'em_uso'         => ['erro', 'Não foi possível excluir: existem locações vinculadas a este registro.'],
        'nao_encontrado' => ['erro', 'Registro não encontrado.'],
        'finalizada'     => ['erro', 'Locações finalizadas fazem parte do histórico e não podem ser canceladas.'],
        'erro'           => ['erro', 'Não foi possível concluir a operação. Tente novamente.'],
    ];
    $codigo = $_GET['msg'] ?? '';
    if (isset($mensagens[$codigo])) {
        echo '<div class="msg ' . $mensagens[$codigo][0] . '">' . h($mensagens[$codigo][1]) . '</div>';
    }
}

?>
