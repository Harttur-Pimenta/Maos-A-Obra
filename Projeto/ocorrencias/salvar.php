<?php
require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$obraId = (int) ($_POST['obra_id'] ?? 0);

if (!$obraId || !obraPertenceAoUsuario($pdo, $obraId)) {
    negarAcesso();
}

$usuarioRegistro = $_POST['usuario_id'] ?: usuarioId();

$sql = "INSERT INTO ocorrencias (
            obra_id,
            usuario_id,
            titulo,
            descricao,
            categoria,
            status,
            prioridade
        ) VALUES (
            :obra_id,
            :usuario_id,
            :titulo,
            :descricao,
            :categoria,
            :status,
            :prioridade
        )";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':obra_id' => $obraId,
    ':usuario_id' => $usuarioRegistro,
    ':titulo' => $_POST['titulo'],
    ':descricao' => $_POST['descricao'],
    ':categoria' => $_POST['categoria'],
    ':status' => $_POST['status'],
    ':prioridade' => $_POST['prioridade']
]);

header('Location: index.php');
exit;
