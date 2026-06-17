<?php
require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$id = (int) ($_POST['id'] ?? 0);

if (!$id || !obraPertenceAoUsuario($pdo, $id)) {
    negarAcesso();
}

$responsavelId = ehAdmin() ? ($_POST['responsavel_id'] ?: null) : usuarioId();

$sql = "UPDATE obras SET 
        nome = :nome,
        endereco = :endereco,
        status = :status,
        responsavel_id = :responsavel_id,
        data_inicio = :data_inicio,
        data_previsao = :data_previsao,
        data_fim = :data_fim,
        orcamento_total = :orcamento_total,
        progresso_pct = :progresso_pct
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nome' => $_POST['nome'],
    ':endereco' => $_POST['endereco'],
    ':status' => $_POST['status'],
    ':responsavel_id' => $responsavelId,
    ':data_inicio' => $_POST['data_inicio'] ?: null,
    ':data_previsao' => $_POST['data_previsao'] ?: null,
    ':data_fim' => $_POST['data_fim'] ?: null,
    ':orcamento_total' => $_POST['orcamento_total'] ?: 0,
    ':progresso_pct' => $_POST['progresso_pct'] ?: 0,
    ':id' => $id
]);

header('Location: index.php');
exit;
