<?php

    $raiz = "../";
    $titulo = "Nova locação";
    include __DIR__ . "/../autenticar.php";

    // Cliente e veículo precisam existir. Só veículos disponíveis podem ser alugados.
    $clientes = $conn->query("SELECT id, nome FROM cliente ORDER BY nome")->fetch_all(MYSQLI_ASSOC);
    $veiculos = $conn->query("SELECT id, marca, modelo, placa, valor_diaria
                              FROM veiculo WHERE status = 'disponivel' ORDER BY marca, modelo")->fetch_all(MYSQLI_ASSOC);

    $l = [
        'cliente_id' => '',
        'veiculo_id' => '',
        'data_retirada' => date('Y-m-d'),
        'data_prevista_devolucao' => '',
    ];
    $erros = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($l as $campo => $valor) {
            $l[$campo] = trim($_POST[$campo] ?? '');
        }

        // Cliente escolhido
        $clienteOk = false;
        foreach ($clientes as $c) {
            if ($c['id'] == $l['cliente_id']) {
                $clienteOk = true;
            }
        }
        if (!$clienteOk) {
            $erros[] = "Escolha um cliente.";
        }

        // Veículo escolhido (precisa estar disponível)
        $veiculoEscolhido = null;
        foreach ($veiculos as $v) {
            if ($v['id'] == $l['veiculo_id']) {
                $veiculoEscolhido = $v;
            }
        }
        if (!$veiculoEscolhido) {
            $erros[] = "Escolha um veículo disponível.";
        }

        // Datas
        $datasOk = dataValida($l['data_retirada']) && dataValida($l['data_prevista_devolucao']);
        if (!$datasOk) {
            $erros[] = "Informe a data de retirada e a data prevista de devolução.";
        } elseif ($l['data_prevista_devolucao'] < $l['data_retirada']) {
            $erros[] = "A devolução prevista não pode ser antes da retirada.";
            $datasOk = false;
        }

        if (!$erros) {
            // Valor previsto = número de diárias (mínimo 1) x valor da diária
            $retirada = new DateTime($l['data_retirada']);
            $prevista = new DateTime($l['data_prevista_devolucao']);
            $dias = max(1, (int)$retirada->diff($prevista)->days);
            $valorTotal = $dias * (float)$veiculoEscolhido['valor_diaria'];

            $clienteId = (int)$l['cliente_id'];
            $veiculoId = (int)$l['veiculo_id'];
            $usuarioId = (int)$_SESSION['id'];

            // Transação: ou grava a locação E marca o veículo como alugado, ou não faz nada
            $conn->begin_transaction();
            try {
                $up = $conn->prepare("UPDATE veiculo SET status = 'alugado' WHERE id = ? AND status = 'disponivel'");
                $up->bind_param("i", $veiculoId);
                $up->execute();

                if ($up->affected_rows !== 1) {
                    throw new Exception("O veículo não está mais disponível.");
                }

                $ins = $conn->prepare("INSERT INTO locacao
                                         (cliente_id, veiculo_id, usuario_id, data_retirada, data_prevista_devolucao, valor_total, status)
                                       VALUES (?, ?, ?, ?, ?, ?, 'ativa')");
                $ins->bind_param("iiissd", $clienteId, $veiculoId, $usuarioId, $l['data_retirada'], $l['data_prevista_devolucao'], $valorTotal);
                $ins->execute();

                $conn->commit();
                header('Location: listar.php?msg=cadastrado');
                exit();
            } catch (Throwable $e) {
                $conn->rollback();
                $erros[] = "Não foi possível registrar a locação. Atualize a página e tente de novo.";
            }
        }
    }

    include __DIR__ . "/../topo.php";

?>

<h1>Nova locação</h1>

<?php if ($erros): ?>
    <div class="msg erro" role="alert">
        <?php foreach ($erros as $erro): ?>
            <?= h($erro) ?><br>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!$clientes): ?>
    <div class="msg erro">Cadastre um cliente antes de criar uma locação. <a href="../cliente/cadastrar.php">Novo cliente</a></div>
<?php elseif (!$veiculos): ?>
    <div class="msg erro">Não há veículos disponíveis no momento. <a href="../veiculo/listar.php">Ver veículos</a></div>
<?php else: ?>
    <form action="" method="post" class="painel">
        <div class="grade">
            <div>
                <label for="cliente_id">Cliente</label>
                <select id="cliente_id" name="cliente_id" required>
                    <option value="">Escolha o cliente</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $l['cliente_id'] == $c['id'] ? 'selected' : '' ?>><?= h($c['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="veiculo_id">Veículo</label>
                <select id="veiculo_id" name="veiculo_id" required>
                    <option value="">Escolha o veículo</option>
                    <?php foreach ($veiculos as $v): ?>
                        <option value="<?= (int)$v['id'] ?>" <?= $l['veiculo_id'] == $v['id'] ? 'selected' : '' ?>>
                            <?= h($v['marca'] . ' ' . $v['modelo'] . ' - ' . $v['placa'] . ' (' . dinheiro($v['valor_diaria']) . '/dia)') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="data_retirada">Data de retirada</label>
                <input type="date" id="data_retirada" name="data_retirada" value="<?= h($l['data_retirada']) ?>" required>
            </div>
            <div>
                <label for="data_prevista_devolucao">Devolução prevista</label>
                <input type="date" id="data_prevista_devolucao" name="data_prevista_devolucao" value="<?= h($l['data_prevista_devolucao']) ?>" required>
            </div>
        </div>
        <p class="suave">O valor é calculado automaticamente: número de diárias x valor da diária do veículo.</p>
        <p>
            <button class="btn" type="submit">Registrar locação</button>
            <a class="btn secundario" href="listar.php">Cancelar</a>
        </p>
    </form>
<?php endif; ?>

<?php include __DIR__ . "/../rodape.php"; ?>
