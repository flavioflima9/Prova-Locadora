<?php // Formulário usado por cadastrar.php e editar.php. Espera $v, $erros, $acao e $statusAtual. ?>

<?php if ($erros): ?>
    <div class="msg erro" role="alert">
        <?php foreach ($erros as $erro): ?>
            <?= h($erro) ?><br>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form action="" method="post" class="painel">
    <div class="grade">
        <div>
            <label for="placa">Placa</label>
            <input type="text" id="placa" name="placa" value="<?= h($v['placa']) ?>" placeholder="ABC1D23" maxlength="8" required>
        </div>
        <div>
            <label for="marca">Marca</label>
            <input type="text" id="marca" name="marca" value="<?= h($v['marca']) ?>" maxlength="50" required>
        </div>
        <div>
            <label for="modelo">Modelo</label>
            <input type="text" id="modelo" name="modelo" value="<?= h($v['modelo']) ?>" maxlength="60" required>
        </div>
        <div>
            <label for="ano">Ano</label>
            <input type="number" id="ano" name="ano" value="<?= h($v['ano']) ?>" min="1990" max="<?= (int)date('Y') + 1 ?>" required>
        </div>
        <div>
            <label for="cor">Cor</label>
            <input type="text" id="cor" name="cor" value="<?= h($v['cor']) ?>" maxlength="30" required>
        </div>
        <div>
            <label for="valor_diaria">Valor da diária (R$)</label>
            <input type="text" id="valor_diaria" name="valor_diaria" value="<?= h($v['valor_diaria']) ?>" placeholder="119.90" inputmode="decimal" required>
        </div>
        <div>
            <label for="status">Situação</label>
            <?php if ($statusAtual === 'alugado'): ?>
                <input type="text" id="status" value="Alugado (muda sozinho quando a locação termina)" disabled>
            <?php else: ?>
                <select id="status" name="status">
                    <option value="disponivel" <?= $v['status'] === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                    <option value="manutencao" <?= $v['status'] === 'manutencao' ? 'selected' : '' ?>>Em manutenção</option>
                </select>
            <?php endif; ?>
        </div>
    </div>
    <p>
        <button class="btn" type="submit"><?= h($acao) ?></button>
        <a class="btn secundario" href="listar.php">Cancelar</a>
    </p>
</form>
