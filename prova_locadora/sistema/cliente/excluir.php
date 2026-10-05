<?php

    $raiz = "../";
    include __DIR__ . "/../autenticar.php";

    // Exclusão só por POST (pelo botão da listagem)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);

        try {
            $stmt = $conn->prepare("DELETE FROM cliente WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            // Chave estrangeira: o cliente tem locações e não pode ser apagado
            header('Location: listar.php?msg=em_uso');
            exit();
        }

        header('Location: listar.php?msg=excluido');
        exit();
    }

    header('Location: listar.php');
    exit();

?>
