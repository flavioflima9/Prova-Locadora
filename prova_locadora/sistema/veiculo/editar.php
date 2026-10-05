<?php

    $raiz = "../";
    $titulo = "Editar veículo";
    include __DIR__ . "/../autenticar.php";
    include __DIR__ . "/_validar.php";

    $id = (int)($_GET['id'] ?? 0);

    $stmt = $conn->prepare("SELECT placa, marca, modelo, ano, cor, valor_diaria, status FROM veiculo WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $v = $stmt->get_result()->fetch_assoc();

    if (!$v) {
        header('Location: listar.php?msg=nao_encontrado');
        exit();
    }

    // Situação gravada no banco. Se estiver "alugado", só a locação pode mudar isso.
    $statusAtual = $v['status'];
    $erros = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $v = lerVeiculoDoPost();

        if ($statusAtual === 'alugado') {
            $v['status'] = 'alugado';
        } elseif ($v['status'] !== 'manutencao') {
            $v['status'] = 'disponivel';
        }

        $erros = validarVeiculo($v);

        if (!$erros) {
            $ano = (int)$v['ano'];
            $diaria = (float)$v['valor_diaria'];

            try {
                $stmt = $conn->prepare("UPDATE veiculo
                                        SET placa = ?, marca = ?, modelo = ?, ano = ?, cor = ?, valor_diaria = ?, status = ?
                                        WHERE id = ?");
                $stmt->bind_param("sssisdsi", $v['placa'], $v['marca'], $v['modelo'], $ano, $v['cor'], $diaria, $v['status'], $id);
                $stmt->execute();

                header('Location: listar.php?msg=atualizado');
                exit();
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $erros[] = "Já existe outro veículo com esta placa.";
                } else {
                    $erros[] = "Não foi possível salvar as alterações. Tente novamente.";
                }
            }
        }
    }

    $acao = "Salvar alterações";
    include __DIR__ . "/../topo.php";

?>

<h1>Editar veículo</h1>

<?php include __DIR__ . "/_form.php"; ?>

<?php include __DIR__ . "/../rodape.php"; ?>
