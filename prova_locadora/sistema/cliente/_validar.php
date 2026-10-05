<?php

// Confere os dados do formulário de cliente e devolve a lista de erros
function validarCliente($c) {
    $erros = [];

    if (mb_strlen($c['nome']) < 3) {
        $erros[] = "Informe o nome completo do cliente.";
    }
    if (!filter_var($c['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Informe um e-mail válido.";
    }
    if (strlen(soDigitos($c['telefone'])) < 10) {
        $erros[] = "Informe o telefone com DDD.";
    }
    if (!cpfValido($c['cpf'])) {
        $erros[] = "CPF inválido.";
    }
    if (strlen(soDigitos($c['cnh'])) != 11) {
        $erros[] = "A CNH deve ter 11 dígitos.";
    }
    if (!dataValida($c['data_nascimento'])) {
        $erros[] = "Informe a data de nascimento.";
    } elseif ($c['data_nascimento'] > date('Y-m-d', strtotime('-18 years'))) {
        $erros[] = "O cliente precisa ter 18 anos ou mais.";
    }

    return $erros;
}

?>
