<?php
$current_page = 'historico';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$busca = $_GET['busca'] ?? '';

$sql = "SELECT
            historico.*,
            usuarios.nome AS usuario
        FROM historico
        LEFT JOIN usuarios ON historico.usuario_id = usuarios.id
        WHERE 1 = 1";

$params = [];

if (ehEngenheiro()) {
    $sql .= " AND (
        (historico.tipo = 'custo' AND historico.referencia_id IN (
            SELECT custos_obra.id
            FROM custos_obra
            INNER JOIN obras ON custos_obra.obra_id = obras.id
            WHERE obras.responsavel_id = :usuario_id
        ))
        OR
        (historico.tipo = 'ocorrencia' AND historico.referencia_id IN (
            SELECT ocorrencias.id
            FROM ocorrencias
            INNER JOIN obras ON ocorrencias.obra_id = obras.id
            WHERE obras.responsavel_id = :usuario_id
        ))
    )";
    $params[':usuario_id'] = usuarioId();
}

if (!empty($busca)) {
    $sql .= " AND (
                historico.tipo LIKE :busca
                OR historico.acao LIKE :busca
                OR historico.descricao LIKE :busca
                OR historico.status_novo LIKE :busca
                OR usuarios.nome LIKE :busca
            )";
    $params[':busca'] = "%{$busca}%";
}

$sql .= " ORDER BY historico.criado_em DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$historicos = $stmt->fetchAll();

include '../configs/header.php';
?>

<main class="main">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Histórico <span>do Sistema</span></h1>
            <p class="page-subtitle">Movimentações detalhadas de custos e ocorrências</p>
        </div>
        <div style="display:flex;gap:1rem;align-items:center;">
            <form method="GET" class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" name="busca" placeholder="Buscar histórico..." value="<?= e($busca) ?>">
            </form>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Ação</th>
                    <th>Descrição</th>
                    <th>Usuário</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historicos)): ?>
                    <tr><td colspan="6" class="table-empty">Nenhuma movimentação encontrada.</td></tr>
                <?php endif; ?>
                <?php foreach ($historicos as $h): ?>
                    <tr>
                        <td><?= e($h['id']) ?></td>
                        <td><?= $h['tipo'] === 'custo' ? '💰 Custo' : '⚠️ Ocorrência' ?></td>
                        <td><?= e($h['acao']) ?></td>
                        <td>
                            <?= e($h['descricao']) ?>
                            <?php if (!empty($h['status_novo'])): ?>
                                <br><small style="color:var(--amber);">Status: <?= e($h['status_novo']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= e($h['usuario'] ?? 'Sistema') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($h['criado_em'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php include '../configs/footer.php'; ?>
