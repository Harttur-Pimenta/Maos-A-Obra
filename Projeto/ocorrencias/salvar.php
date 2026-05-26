<?php
require_once '../configs/banco.php';

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
    ':obra_id' => $_POST['obra_id'],
    ':usuario_id' => $_POST['usuario_id'] ?: null,
    ':titulo' => $_POST['titulo'],
    ':descricao' => $_POST['descricao'],
    ':categoria' => $_POST['categoria'],
    ':status' => $_POST['status'],
    ':prioridade' => $_POST['prioridade']
]);

header('Location: index.php'); 
exit;