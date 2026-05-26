<?php
require_once '../configs/banco.php';

if (isset($_GET['id'])) {

    $sql = "DELETE FROM obras WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id' => $_GET['id']
    ]);
}

header('Location: index.php');
exit;