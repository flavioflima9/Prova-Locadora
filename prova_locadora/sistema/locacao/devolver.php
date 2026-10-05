<?php

    $raiz = "../";
    include __DIR__ . "/../autenticar.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);

        // Um único UPDATE encerra a locação, recalcula o valor pelos dias reais
        // e libera o veículo (as duas tabelas mudam juntas).
        $stmt = $conn->prepare("UPDATE locacao l
                                INNER JOIN veiculo v ON v.id = l.veiculo_id
                                SET l.data_devolucao = CURDATE(),
                                    l.status = 'finalizada',
                                    l.valor_total = GREATEST(1, DATEDIFF(CURDATE(), l.data_retirada)) * v.valor_diaria,
                                    v.status = 'disponivel'
                                WHERE l.id = ? AND l.status = 'ativa'");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header('Location: listar.php?msg=devolvido');
        } else {
            header('Location: listar.php?msg=nao_encontrado');
        }
        exit();
    }

    header('Location: listar.php');
    exit();

?>
