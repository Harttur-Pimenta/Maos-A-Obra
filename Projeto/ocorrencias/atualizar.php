<?php
require_once '../configs/banco.php';

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
    ':obra_id' => $_POST['obra_id'],
    ':usuario_id' => $_POST['usuario_id'] ?: null,
    ':titulo' => $_POST['titulo'],
    ':descricao' => $_POST['descricao'],
    ':categoria' => $_POST['categoria'],
    ':status' => $_POST['status'],
    ':prioridade' => $_POST['prioridade'],
    ':id' => $_POST['id']
]);

header('Location: index.php');
exit;