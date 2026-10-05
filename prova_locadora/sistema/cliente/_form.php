<?php // Formulário usado por cadastrar.php e editar.php. Espera $c, $erros e $acao. ?>

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
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome" value="<?= h($c['nome']) ?>" maxlength="100" required>
        </div>
        <div>
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= h($c['email']) ?>" maxlength="120" required>
        </div>
        <div>
            <label for="telefone">Telefone</label>
            <input type="tel" id="telefone" name="telefone" value="<?= h($c['telefone']) ?>" placeholder="(81) 99999-9999" maxlength="20" required>
        </div>
        <div>
            <label for="data_nascimento">Data de nascimento</label>
            <input type="date" id="data_nascimento" name="data_nascimento" value="<?= h($c['data_nascimento']) ?>" required>
        </div>
        <div>
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" value="<?= h($c['cpf']) ?>" placeholder="000.000.000-00" inputmode="numeric" maxlength="14" required>
        </div>
        <div>
            <label for="cnh">CNH</label>
            <input type="text" id="cnh" name="cnh" value="<?= h($c['cnh']) ?>" placeholder="11 dígitos" inputmode="numeric" maxlength="11" required>
        </div>
    </div>
    <p>
        <button class="btn" type="submit"><?= h($acao) ?></button>
        <a class="btn secundario" href="listar.php">Cancelar</a>
    </p>
</form>
