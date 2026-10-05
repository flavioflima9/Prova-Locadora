<?php

    $raiz = "../";
    $titulo = "Editar cliente";
    include __DIR__ . "/../autenticar.php";
    include __DIR__ . "/_validar.php";

    $id = (int)($_GET['id'] ?? 0);
    $chave = CHAVE_CRIPTO;

    // Carrega o cliente (CPF e CNH são descriptografados só para o formulário)
    $stmt = $conn->prepare("SELECT nome, email, telefone,
                                   CAST(AES_DECRYPT(cpf, ?) AS CHAR) AS cpf,
                                   CAST(AES_DECRYPT(cnh, ?) AS CHAR) AS cnh,
                                   data_nascimento
                            FROM cliente WHERE id = ?");
    $stmt->bind_param("ssi", $chave, $chave, $id);
    $stmt->execute();
    $c = $stmt->get_result()->fetch_assoc();

    if (!$c) {
        header('Location: listar.php?msg=nao_encontrado');
        exit();
    }

    $c['cpf'] = formatarCpf($c['cpf']);
    $erros = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach (['nome', 'email', 'telefone', 'cpf', 'cnh', 'data_nascimento'] as $campo) {
            $c[$campo] = trim($_POST[$campo] ?? '');
        }

        $erros = validarCliente($c);

        if (!$erros) {
            $cpf = soDigitos($c['cpf']);
            $cnh = soDigitos($c['cnh']);

            try {
                $stmt = $conn->prepare("UPDATE cliente
                                        SET nome = ?, email = ?, telefone = ?,
                                            cpf = AES_ENCRYPT(?, ?), cnh = AES_ENCRYPT(?, ?),
                                            data_nascimento = ?
                                        WHERE id = ?");
                $stmt->bind_param("ssssssssi", $c['nome'], $c['email'], $c['telefone'], $cpf, $chave, $cnh, $chave, $c['data_nascimento'], $id);
                $stmt->execute();

                header('Location: listar.php?msg=atualizado');
                exit();
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $erros[] = "Já existe outro cliente com este CPF ou e-mail.";
                } else {
                    $erros[] = "Não foi possível salvar as alterações. Tente novamente.";
                }
            }
        }
    }

    $acao = "Salvar alterações";
    include __DIR__ . "/../topo.php";

?>

<h1>Editar cliente</h1>

<?php include __DIR__ . "/_form.php"; ?>

<?php include __DIR__ . "/../rodape.php"; ?>
