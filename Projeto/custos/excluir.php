<?php
require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

if (!$id || !custoPertenceAoUsuario($pdo, $id)) {
    negarAcesso();
}

$stmt = $pdo->prepare('DELETE FROM custos_obra WHERE id = :id');
$stmt->execute([':id' => $id]);

header('Location: index.php');
exit;
