<?php

    // Incluído no início de toda página que exige login.
    // A página define $raiz antes: "" na pasta principal e "../" nas subpastas.

    session_start();
    $raiz = $raiz ?? "";

    include_once __DIR__ . "/conexao.php";
    include_once __DIR__ . "/funcoes.php";

    // Sem login: volta para a tela de login
    if (empty($_SESSION['email'])) {
        header('Location: ' . $raiz . 'login.php');
        exit();
    }

    // Sessão parada por mais de TEMPO_SESSAO segundos: expira
    if (isset($_SESSION['ultimo_acesso']) && (time() - $_SESSION['ultimo_acesso']) > TEMPO_SESSAO) {
        session_unset();
        session_destroy();
        header('Location: ' . $raiz . 'login.php?expirou=1');
        exit();
    }

    // Cada acesso renova o prazo
    $_SESSION['ultimo_acesso'] = time();

?>
