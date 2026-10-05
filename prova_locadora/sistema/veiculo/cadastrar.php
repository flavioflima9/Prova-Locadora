<?php

    $raiz = "../";
    $titulo = "Novo veículo";
    include __DIR__ . "/../autenticar.php";
    include __DIR__ . "/_validar.php";

    $v = ['placa' => '', 'marca' => '', 'modelo' => '', 'ano' => '', 'cor' => '', 'valor_diaria' => '', 'status' => 'disponivel'];
    $statusAtual = '';
    $erros = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $v = lerVeiculoDoPost();

        // Um veículo novo nunca nasce "alugado": isso só acontece por uma locação
        if ($v['status'] !== 'manutencao') {
            $v['status'] = 'disponivel';
        }

        $erros = validarVeiculo($v);

        if (!$erros) {
            $ano = (int)$v['ano'];
            $diaria = (float)$v['valor_diaria'];

            try {
                $stmt = $conn->prepare("INSERT INTO veiculo (placa, marca, modelo, ano, cor, valor_diaria, status)
                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssisds", $v['placa'], $v['marca'], $v['modelo'], $ano, $v['cor'], $diaria, $v['status']);
                $stmt->execute();

                header('Location: listar.php?msg=cadastrado');
                exit();
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $erros[] = "Já existe um veículo com esta placa.";
                } else {
                    $erros[] = "Não foi possível salvar o veículo. Tente novamente.";
                }
            }
        }
    }

    $acao = "Cadastrar veículo";
    include __DIR__ . "/../topo.php";

?>

<h1>Novo veículo</h1>

<?php include __DIR__ . "/_form.php"; ?>

<?php include __DIR__ . "/../rodape.php"; ?>
