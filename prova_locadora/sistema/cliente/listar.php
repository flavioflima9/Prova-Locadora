<?php

    $raiz = "../";
    $titulo = "Clientes";
    include __DIR__ . "/../autenticar.php";

    $busca = trim($_GET['busca'] ?? '');
    $chave = CHAVE_CRIPTO;

    if ($busca !== '') {
        // Busca por nome, e-mail, telefone ou CPF completo.
        // O CPF está criptografado, então a busca compara o valor criptografado.
        $termo = "%" . addcslashes($busca, "%_\\") . "%";
        $cpfBuscado = soDigitos($busca);

        $sql = "SELECT id, nome, email, telefone, CAST(AES_DECRYPT(cpf, ?) AS CHAR) AS cpf
                FROM cliente
                WHERE nome LIKE ? OR email LIKE ? OR telefone LIKE ? OR cpf = AES_ENCRYPT(?, ?)
                ORDER BY nome";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $chave, $termo, $termo, $termo, $cpfBuscado, $chave);
    } else {
        $sql = "SELECT id, nome, email, telefone, CAST(AES_DECRYPT(cpf, ?) AS CHAR) AS cpf
                FROM cliente
                ORDER BY nome";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $chave);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    include __DIR__ . "/../topo.php";

?>

<div class="topo-lista">
    <h1>Clientes</h1>
    <a class="btn" href="cadastrar.php">Novo cliente</a>
</div>

<?php mensagemFlash(); ?>

<form class="busca" method="get" action="">
    <input type="search" name="busca" value="<?= h($busca) ?>" placeholder="Buscar por nome, e-mail, telefone ou CPF" aria-label="Termo de busca">
    <button class="btn" type="submit">Buscar</button>
    <a class="btn secundario" href="listar.php">Limpar</a>
</form>

<?php if ($busca !== ''): ?>
    <p class="suave"><?= (int)$resultado->num_rows ?> cliente(s) encontrado(s) para "<?= h($busca) ?>".</p>
<?php endif; ?>

<div class="tabela-rolagem">
<?php if ($resultado->num_rows === 0): ?>
    <p class="vazio">Nenhum cliente encontrado.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>CPF</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($c = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= h($c['nome']) ?></td>
                <td><?= h($c['email']) ?></td>
                <td><?= h($c['telefone']) ?></td>
                <td><?= h(mascararCpf($c['cpf'])) ?></td>
                <td class="acoes">
                    <a class="btn secundario pequeno" href="editar.php?id=<?= (int)$c['id'] ?>">Editar</a>
                    <form class="linha" method="post" action="excluir.php" onsubmit="return confirm('Excluir este cliente?');">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
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
