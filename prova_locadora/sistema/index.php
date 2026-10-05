<?php 

    $raiz = "";
    $titulo = "Início";
    include __DIR__ . "/autenticar.php";

    // Números rápidos para a tela principal
    $totalClientes = $conn->query("SELECT COUNT(*) AS total FROM cliente")->fetch_assoc()['total'];
    $totalDisponiveis = $conn->query("SELECT COUNT(*) AS total FROM veiculo WHERE status = 'disponivel'")->fetch_assoc()['total'];
    $totalAtivas = $conn->query("SELECT COUNT(*) AS total FROM locacao WHERE status = 'ativa'")->fetch_assoc()['total'];

    include __DIR__ . "/topo.php";

?>

<h1>Bem vindo, <?= h($_SESSION['nome']) ?>!</h1>
<p class="suave">Escolha o que deseja gerenciar.</p>

<div class="atalhos">
    <a class="atalho" href="cliente/listar.php">
        <strong>Clientes</strong>
        <span><?= (int)$totalClientes ?> cadastrados. Cadastrar, buscar e editar.</span>
    </a>
    <a class="atalho" href="veiculo/listar.php">
        <strong>Veículos</strong>
        <span><?= (int)$totalDisponiveis ?> disponíveis para locação.</span>
    </a>
    <a class="atalho" href="locacao/listar.php">
        <strong>Locações</strong>
        <span><?= (int)$totalAtivas ?> em andamento. Nova locação e devoluções.</span>
    </a>
</div>

<a class="btn secundario" href="logout.php">Sair</a>

<?php include __DIR__ . "/rodape.php"; ?>
