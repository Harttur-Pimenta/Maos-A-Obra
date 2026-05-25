<?php
require_once '../configs/banco.php';

$sql = "INSERT INTO obras (
    nome, endereco, status, responsavel_id,
    data_inicio, data_previsao, data_fim,
    orcamento_total, progresso_pct
) VALUES (
    :nome, :endereco, :status, :responsavel_id,
    :data_inicio, :data_previsao, :data_fim,
    :orcamento_total, :progresso_pct
)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nome' => $_POST['nome'],
    ':endereco' => $_POST['endereco'],
    ':status' => $_POST['status'],
    ':responsavel_id' => $_POST['responsavel_id'] ?: null,
    ':data_inicio' => $_POST['data_inicio'] ?: null,
    ':data_previsao' => $_POST['data_previsao'] ?: null,
    ':data_fim' => $_POST['data_fim'] ?: null,
    ':orcamento_total' => $_POST['orcamento_total'] ?: 0,
    ':progresso_pct' => $_POST['progresso_pct'] ?: 0
]);

header('Location: index.php');
exit;