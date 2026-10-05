<?php

session_start();

include_once __DIR__ . "/conexao.php";
include_once __DIR__ . "/funcoes.php";

// Quem já está logado vai direto para a tela principal
if (!empty($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$erro = "";
$email = "";

if (isset($_GET['expirou'])) {
    $erro = "Sua sessão expirou por inatividade. Entre novamente.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = "Informe o e-mail e a senha.";
    } else {
        // Consulta preparada: o e-mail digitado nunca vira parte do SQL
        $stmt = $conn->prepare("SELECT id, nome, email, senha FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();

        // password_verify compara a senha digitada com o hash guardado
        if ($resultado && password_verify($senha, $resultado['senha'])) {
            session_regenerate_id(true);
            $_SESSION['email'] = $resultado['email'];
            $_SESSION['nome'] = $resultado['nome'];
            $_SESSION['id'] = $resultado['id'];
            $_SESSION['ultimo_acesso'] = time();
            header('Location: index.php');
            exit();
        } else {
            // Mesma mensagem para e-mail ou senha errados: não revela qual deles falhou
            $erro = "E-mail ou senha incorretos.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar - Locadora</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<main class="login-caixa painel">
    <h1>Locadora de Veículos</h1>
    <p class="suave">Entre com seu e-mail e senha.</p>

    <?php if ($erro): ?>
        <div class="msg erro" role="alert"><?= h($erro) ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" placeholder="Informe seu email" value="<?= h($email) ?>" required>

        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha" placeholder="Informe a senha" required>

        <p><button class="btn" type="submit">Entrar</button></p>
    </form>
</main>
</body>
</html>
