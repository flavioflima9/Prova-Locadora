<?php

    $raiz = "../";
    $titulo = "Novo cliente";
    include __DIR__ . "/../autenticar.php";
    include __DIR__ . "/_validar.php";

    $c = ['nome' => '', 'email' => '', 'telefone' => '', 'cpf' => '', 'cnh' => '', 'data_nascimento' => ''];
    $erros = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($c as $campo => $valor) {
            $c[$campo] = trim($_POST[$campo] ?? '');
        }

        $erros = validarCliente($c);

        if (!$erros) {
            $cpf = soDigitos($c['cpf']);
            $cnh = soDigitos($c['cnh']);
            $chave = CHAVE_CRIPTO;

            try {
                // CPF e CNH são gravados criptografados
                $stmt = $conn->prepare("INSERT INTO cliente (nome, email, telefone, cpf, cnh, data_nascimento)
                                        VALUES (?, ?, ?, AES_ENCRYPT(?, ?), AES_ENCRYPT(?, ?), ?)");
                $stmt->bind_param("ssssssss", $c['nome'], $c['email'], $c['telefone'], $cpf, $chave, $cnh, $chave, $c['data_nascimento']);
                $stmt->execute();

                header('Location: listar.php?msg=cadastrado');
                exit();
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $erros[] = "Já existe um cliente com este CPF ou e-mail.";
                } else {
                    $erros[] = "Não foi possível salvar o cliente. Tente novamente.";
                }
            }
        }
    }

    $acao = "Cadastrar cliente";
    include __DIR__ . "/../topo.php";

?>

<h1>Novo cliente</h1>

<?php include __DIR__ . "/_form.php"; ?>

<?php include __DIR__ . "/../rodape.php"; ?>
