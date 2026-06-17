<?php
require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$id = (int) ($_POST['id'] ?? 0);
$obraId = (int) ($_POST['obra_id'] ?? 0);

if (!$id || !$obraId || !custoPertenceAoUsuario($pdo, $id) || !obraPertenceAoUsuario($pdo, $obraId)) {
    negarAcesso();
}

$usuarioLancamento = $_POST['usuario_id'] ?: usuarioId();

$sql = "UPDATE custos_obra SET
            obra_id = :obra_id,
            usuario_id = :usuario_id,
            descricao = :descricao,
            tipo = :tipo,
            qtd_planejada = :qtd_planejada,
            qtd_realizada = :qtd_realizada,
            valor_planejado = :valor_planejado,
            valor_realizado = :valor_realizado,
            data_lancamento = :data_lancamento
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':obra_id' => $obraId,
    ':usuario_id' => $usuarioLancamento,
    ':descricao' => $_POST['descricao'],
    ':tipo' => $_POST['tipo'],
    ':qtd_planejada' => $_POST['qtd_planejada'] ?: 0,
    ':qtd_realizada' => $_POST['qtd_realizada'] ?: 0,
    ':valor_planejado' => $_POST['valor_planejado'] ?: 0,
    ':valor_realizado' => $_POST['valor_realizado'] ?: 0,
    ':data_lancamento' => $_POST['data_lancamento'] ?: null,
    ':id' => $id
]);

header('Location: index.php');
exit;
