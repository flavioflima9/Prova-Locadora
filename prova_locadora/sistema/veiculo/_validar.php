<?php

// Confere os dados do formulário de veículo e devolve a lista de erros
function validarVeiculo($v) {
    $erros = [];
    $anoMaximo = (int)date('Y') + 1;

    if (!preg_match('/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/', $v['placa'])) {
        $erros[] = "Placa inválida. Use o formato ABC1D23 ou ABC1234.";
    }
    if (mb_strlen($v['marca']) < 2) {
        $erros[] = "Informe a marca.";
    }
    if (mb_strlen($v['modelo']) < 1) {
        $erros[] = "Informe o modelo.";
    }
    if (!ctype_digit($v['ano']) || (int)$v['ano'] < 1990 || (int)$v['ano'] > $anoMaximo) {
        $erros[] = "Informe um ano entre 1990 e " . $anoMaximo . ".";
    }
    if (mb_strlen($v['cor']) < 3) {
        $erros[] = "Informe a cor.";
    }
    if (!is_numeric($v['valor_diaria']) || (float)$v['valor_diaria'] <= 0) {
        $erros[] = "Informe um valor de diária maior que zero.";
    }
    if (!in_array($v['status'], ['disponivel', 'alugado', 'manutencao'], true)) {
        $erros[] = "Situação inválida.";
    }

    return $erros;
}

// Lê os campos do formulário já limpos (placa em maiúsculas, vírgula vira ponto)
function lerVeiculoDoPost() {
    return [
        'placa'        => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $_POST['placa'] ?? '')),
        'marca'        => trim($_POST['marca'] ?? ''),
        'modelo'       => trim($_POST['modelo'] ?? ''),
        'ano'          => trim($_POST['ano'] ?? ''),
        'cor'          => trim($_POST['cor'] ?? ''),
        'valor_diaria' => str_replace(',', '.', trim($_POST['valor_diaria'] ?? '')),
        'status'       => $_POST['status'] ?? 'disponivel',
    ];
}

?>
