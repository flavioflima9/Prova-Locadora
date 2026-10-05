<?php

    $raiz = "../";
    include __DIR__ . "/../autenticar.php";

    // Cancela uma locação em andamento (por POST, pelo botão da listagem)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);

        $stmt = $conn->prepare("SELECT veiculo_id, status FROM locacao WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $locacao = $stmt->get_result()->fetch_assoc();

        if (!$locacao) {
            header('Location: listar.php?msg=nao_encontrado');
            exit();
        }

        // Finalizadas ficam como histórico
        if ($locacao['status'] !== 'ativa') {
            header('Location: listar.php?msg=finalizada');
            exit();
        }

        $conn->begin_transaction();
        try {
            $del = $conn->prepare("DELETE FROM locacao WHERE id = ?");
            $del->bind_param("i", $id);
            $del->execute();

            $lib = $conn->prepare("UPDATE veiculo SET status = 'disponivel' WHERE id = ?");
            $lib->bind_param("i", $locacao['veiculo_id']);
            $lib->execute();

            $conn->commit();
            header('Location: listar.php?msg=cancelada');
        } catch (Throwable $e) {
            $conn->rollback();
            header('Location: listar.php?msg=erro');
        }
        exit();
    }

    header('Location: listar.php');
    exit();

?>
