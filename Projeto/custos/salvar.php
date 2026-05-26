<?php
require_once '../configs/banco.php';

$sql = "INSERT INTO custos_obra (
    obra_id, usuario_id, descricao, tipo,
    qtd_planejada, qtd_realizada, valor_planejado,
    valor_realizado, data_lancamento
) VALUES (
    :obra_id, :usuario_id, :descricao, :tipo,
    :qtd_planejada, :qtd_realizada, :valor_planejado,
    :valor_realizado, :data_lancamento
)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':obra_id' => $_POST['obra_id'],
    ':usuario_id' => $_POST['usuario_id'] ?: null,
    ':descricao' => $_POST['descricao'],
    ':tipo' => $_POST['tipo'],
    ':qtd_planejada' => $_POST['qtd_planejada'] ?: 0,
    ':qtd_realizada' => $_POST['qtd_realizada'] ?: 0,
    ':valor_planejado' => $_POST['valor_planejado'] ?: 0,
    ':valor_realizado' => $_POST['valor_realizado'] ?: 0,
    ':data_lancamento' => $_POST['data_lancamento'] ?: null
]);

header('Location: index.php');
exit;