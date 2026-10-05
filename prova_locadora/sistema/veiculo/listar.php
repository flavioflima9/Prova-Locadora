<?php

    $raiz = "../";
    $titulo = "Veículos";
    include __DIR__ . "/../autenticar.php";

    $busca = trim($_GET['busca'] ?? '');

    if ($busca !== '') {
        $termo = "%" . addcslashes($busca, "%_\\") . "%";
        $stmt = $conn->prepare("SELECT id, placa, marca, modelo, ano, cor, valor_diaria, status
                                FROM veiculo
                                WHERE placa LIKE ? OR marca LIKE ? OR modelo LIKE ? OR cor LIKE ?
                                ORDER BY marca, modelo");
        $stmt->bind_param("ssss", $termo, $termo, $termo, $termo);
    } else {
        $stmt = $conn->prepare("SELECT id, placa, marca, modelo, ano, cor, valor_diaria, status
                                FROM veiculo
                                ORDER BY marca, modelo");
    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    include __DIR__ . "/../topo.php";

?>

<div class="topo-lista">
    <h1>Veículos</h1>
    <a class="btn" href="cadastrar.php">Novo veículo</a>
</div>

<?php mensagemFlash(); ?>

<form class="busca" method="get" action="">
    <input type="search" name="busca" value="<?= h($busca) ?>" placeholder="Buscar por placa, marca, modelo ou cor" aria-label="Termo de busca">
    <button class="btn" type="submit">Buscar</button>
    <a class="btn secundario" href="listar.php">Limpar</a>
</form>

<?php if ($busca !== ''): ?>
    <p class="suave"><?= (int)$resultado->num_rows ?> veículo(s) encontrado(s) para "<?= h($busca) ?>".</p>
<?php endif; ?>

<div class="tabela-rolagem">
<?php if ($resultado->num_rows === 0): ?>
    <p class="vazio">Nenhum veículo encontrado.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Placa</th>
                <th>Veículo</th>
                <th>Ano</th>
                <th>Cor</th>
                <th>Diária</th>
                <th>Situação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($v = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= h($v['placa']) ?></td>
                <td><?= h($v['marca'] . ' ' . $v['modelo']) ?></td>
                <td><?= (int)$v['ano'] ?></td>
                <td><?= h($v['cor']) ?></td>
                <td><?= h(dinheiro($v['valor_diaria'])) ?></td>
                <td><?= seloStatusVeiculo($v['status']) ?></td>
                <td class="acoes">
                    <a class="btn secundario pequeno" href="editar.php?id=<?= (int)$v['id'] ?>">Editar</a>
                    <form class="linha" method="post" action="excluir.php" onsubmit="return confirm('Excluir este veículo?');">
                        <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                        <button class="btn perigo pequeno" type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>

<?php include __DIR__ . "/../rodape.php"; ?>
