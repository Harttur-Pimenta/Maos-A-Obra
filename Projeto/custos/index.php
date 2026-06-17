<?php
$current_page = 'custos';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$busca = $_GET['busca'] ?? '';

$sql = "SELECT
            custos_obra.*,
            obras.nome AS obra,
            usuarios.nome AS responsavel
        FROM custos_obra
        LEFT JOIN obras ON custos_obra.obra_id = obras.id
        LEFT JOIN usuarios ON custos_obra.usuario_id = usuarios.id
        WHERE 1 = 1";

$params = [];

if (ehEngenheiro()) {
    $sql .= " AND obras.responsavel_id = :usuario_id";
    $params[':usuario_id'] = usuarioId();
}

if (!empty($busca)) {
    $sql .= " AND (
                    custos_obra.descricao LIKE :busca
                    OR custos_obra.tipo LIKE :busca
                    OR obras.nome LIKE :busca
                    OR usuarios.nome LIKE :busca
                )";
    $params[':busca'] = "%{$busca}%";
}

$sql .= " ORDER BY custos_obra.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$custos = $stmt->fetchAll();

include '../configs/header.php';
?>

<main class="main">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Custos e Materiais <span>Gerais</span></h1>
            <p class="page-subtitle">Visualizar, inserir e editar gastos</p>
        </div>
        <div style="display:flex;gap:1rem;align-items:center;">
            <form method="GET" class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" name="busca" placeholder="Buscar custo..." value="<?= e($busca) ?>">
            </form>
            <a href="../custos/cadastrar.php" class="btn btn-primary">+ Novo Custo</a>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Obra</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Responsável</th>
                    <th>Qtd Planejada</th>
                    <th>Qtd Realizada</th>
                    <th>Valor Planejado</th>
                    <th>Valor Realizado</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($custos)): ?>
                    <tr><td colspan="11" class="table-empty">Nenhum custo encontrado.</td></tr>
                <?php endif; ?>
                <?php foreach ($custos as $custo): ?>
                    <tr>
                        <td><?= e($custo['id']) ?></td>
                        <td><?= e($custo['obra'] ?? '-') ?></td>
                        <td><?= e($custo['descricao']) ?></td>
                        <td><span class="badge badge-info"><?= e($custo['tipo']) ?></span></td>
                        <td><?= e($custo['responsavel'] ?? 'Não definido') ?></td>
                        <td><?= e($custo['qtd_planejada']) ?></td>
                        <td><?= e($custo['qtd_realizada']) ?></td>
                        <td>R$ <?= number_format($custo['valor_planejado'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($custo['valor_realizado'], 2, ',', '.') ?></td>
                        <td><?= !empty($custo['data_lancamento']) ? date('d/m/Y', strtotime($custo['data_lancamento'])) : '-' ?></td>
                        <td style="display:flex;gap:1rem;align-items:center;">
                            <a href="editar.php?id=<?= e($custo['id']) ?>" class="btn btn-secondary btn-sm">Editar</a>
                            <a href="excluir.php?id=<?= e($custo['id']) ?>" class="btn-delete btn-sm" onclick="return confirm('Deseja realmente excluir este custo?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php include '../configs/footer.php'; ?>
