<?php

    $raiz = "../";
    $titulo = "Locações";
    include __DIR__ . "/../autenticar.php";

    // Ordenada por data de retirada. Por padrão, a mais recente primeiro;
    // clicar no título da coluna inverte a ordem.
    $ordem = (($_GET['ordem'] ?? 'desc') === 'asc') ? 'ASC' : 'DESC';
    $proxima = ($ordem === 'ASC') ? 'desc' : 'asc';
    $seta = ($ordem === 'ASC') ? '▲' : '▼';

    // JOIN traz junto os dados do cliente e do veículo de cada locação
    $sql = "SELECT l.id, l.data_retirada, l.data_prevista_devolucao, l.data_devolucao,
                   l.valor_total, l.status,
                   c.nome AS cliente,
                   v.marca, v.modelo, v.placa
            FROM locacao l
            INNER JOIN cliente c ON c.id = l.cliente_id
            INNER JOIN veiculo v ON v.id = l.veiculo_id
            ORDER BY l.data_retirada $ordem, l.id $ordem";
    $resultado = $conn->query($sql);

    $hoje = date('Y-m-d');

    include __DIR__ . "/../topo.php";

?>

<div class="topo-lista">
    <h1>Locações</h1>
    <a class="btn" href="cadastrar.php">Nova locação</a>
</div>

<?php mensagemFlash(); ?>

<div class="tabela-rolagem">
<?php if ($resultado->num_rows === 0): ?>
    <p class="vazio">Nenhuma locação cadastrada.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nº</th>
                <th>Cliente</th>
                <th>Veículo</th>
                <th><a href="?ordem=<?= $proxima ?>" title="Inverter a ordem">Retirada <?= $seta ?></a></th>
                <th>Devolução prevista</th>
                <th>Devolvido em</th>
                <th>Valor</th>
                <th>Situação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($l = $resultado->fetch_assoc()): ?>
            <?php
                if ($l['status'] === 'finalizada') {
                    $selo = ['verde', 'Finalizada'];
                } elseif ($l['data_prevista_devolucao'] < $hoje) {
                    $selo = ['vermelho', 'Atrasada'];
                } else {
                    $selo = ['azul', 'Em andamento'];
                }
            ?>
            <tr>
                <td><?= (int)$l['id'] ?></td>
                <td><?= h($l['cliente']) ?></td>
                <td><?= h($l['marca'] . ' ' . $l['modelo']) ?><br><small class="suave"><?= h($l['placa']) ?></small></td>
                <td><?= h(formatarData($l['data_retirada'])) ?></td>
                <td><?= h(formatarData($l['data_prevista_devolucao'])) ?></td>
                <td><?= h(formatarData($l['data_devolucao'])) ?></td>
                <td><?= h(dinheiro($l['valor_total'])) ?></td>
                <td><span class="selo <?= $selo[0] ?>"><?= h($selo[1]) ?></span></td>
                <td class="acoes">
                    <?php if ($l['status'] === 'ativa'): ?>
                        <form class="linha" method="post" action="devolver.php" onsubmit="return confirm('Registrar a devolução deste veículo hoje?');">
                            <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                            <button class="btn pequeno" type="submit">Devolver</button>
                        </form>
                        <form class="linha" method="post" action="excluir.php" onsubmit="return confirm('Cancelar esta locação?');">
                            <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                            <button class="btn perigo pequeno" type="submit">Cancelar</button>
                        </form>
                    <?php else: ?>
                        <span class="suave">Histórico</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>

<?php include __DIR__ . "/../rodape.php"; ?>
