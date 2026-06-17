<?php
require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$params = [];
$filtro = '';

if (ehEngenheiro()) {
    $filtro = ' AND obras.responsavel_id = :usuario_id';
    $params[':usuario_id'] = usuarioId();
}

$sql = "SELECT obras.nome AS obra,
               usuarios.nome AS responsavel,
               obras.status,
               obras.progresso_pct,
               obras.data_previsao,
               COALESCE(SUM(custos_obra.valor_realizado), 0) AS custo_realizado
        FROM obras
        LEFT JOIN usuarios ON obras.responsavel_id = usuarios.id
        LEFT JOIN custos_obra ON custos_obra.obra_id = obras.id
        WHERE 1 = 1" . $filtro . "
        GROUP BY obras.id, usuarios.nome
        ORDER BY obras.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$dados = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=relatorio_obras.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['Obra', 'Responsável', 'Status', 'Progresso %', 'Previsão', 'Custo Realizado'], ';');

foreach ($dados as $linha) {
    fputcsv($out, [
        $linha['obra'],
        $linha['responsavel'] ?? 'Não definido',
        $linha['status'],
        $linha['progresso_pct'],
        $linha['data_previsao'],
        number_format($linha['custo_realizado'], 2, ',', '.')
    ], ';');
}

fclose($out);
exit;
