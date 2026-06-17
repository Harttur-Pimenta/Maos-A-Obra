<?php
require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

if (!$id || !ocorrenciaPertenceAoUsuario($pdo, $id)) {
    negarAcesso();
}

$stmt = $pdo->prepare('DELETE FROM ocorrencias WHERE id = :id');
$stmt->execute([':id' => $id]);

header('Location: index.php');
exit;
