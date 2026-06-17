<?php
require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$id = (int) ($_POST['id'] ?? 0);
$obraId = (int) ($_POST['obra_id'] ?? 0);

if (!$id || !$obraId || !ocorrenciaPertenceAoUsuario($pdo, $id) || !obraPertenceAoUsuario($pdo, $obraId)) {
    negarAcesso();
}

$usuarioRegistro = $_POST['usuario_id'] ?: usuarioId();

$sql = "UPDATE ocorrencias SET
            obra_id = :obra_id,
            usuario_id = :usuario_id,
            titulo = :titulo,
            descricao = :descricao,
            categoria = :categoria,
            status = :status,
            prioridade = :prioridade
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':obra_id' => $obraId,
    ':usuario_id' => $usuarioRegistro,
    ':titulo' => $_POST['titulo'],
    ':descricao' => $_POST['descricao'],
    ':categoria' => $_POST['categoria'],
    ':status' => $_POST['status'],
    ':prioridade' => $_POST['prioridade'],
    ':id' => $id
]);

header('Location: index.php');
exit;
