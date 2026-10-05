<?php // Cabeçalho comum. Espera $raiz e (opcional) $titulo definidos pela página. ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h(($titulo ?? 'Início') . ' - Locadora') ?></title>
    <link rel="stylesheet" href="<?= $raiz ?>estilo.css">
</head>
<body>
<header class="barra">
    <a class="marca" href="<?= $raiz ?>index.php">Locadora</a>
    <nav>
        <a href="<?= $raiz ?>cliente/listar.php">Clientes</a>
        <a href="<?= $raiz ?>veiculo/listar.php">Veículos</a>
        <a href="<?= $raiz ?>locacao/listar.php">Locações</a>
        <a href="<?= $raiz ?>logout.php">Sair</a>
    </nav>
</header>
<main class="conteudo">
