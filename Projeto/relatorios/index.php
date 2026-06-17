<?php
$current_page = 'relatorios';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$params = [];
$filtro = '';

if (ehEngenheiro()) {
    $filtro = ' AND obras.responsavel_id = :usuario_id';
    $params[':usuario_id'] = usuarioId();
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM obras WHERE 1 = 1" . $filtro);
$stmt->execute($params);
$totalObras = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM obras WHERE status = 'ativa'" . $filtro);
$stmt->execute($params);
$obrasAtivas = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(custos_obra.valor_planejado), 0) AS planejado,
                              COALESCE(SUM(custos_obra.valor_realizado), 0) AS realizado
                       FROM custos_obra
                       INNER JOIN obras ON custos_obra.obra_id = obras.id
                       WHERE 1 = 1" . $filtro);
$stmt->execute($params);
$totaisCustos = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*)
                       FROM ocorrencias
                       INNER JOIN obras ON ocorrencias.obra_id = obras.id
                       WHERE ocorrencias.status != 'resolvida'" . $filtro);
$stmt->execute($params);
$ocorrenciasPendentes = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT obras.nome,
                              obras.status,
                              obras.progresso_pct,
                              obras.data_previsao,
                              usuarios.nome AS responsavel,
                              COALESCE(SUM(custos_obra.valor_realizado), 0) AS custo_realizado
                       FROM obras
                       LEFT JOIN usuarios ON obras.responsavel_id = usuarios.id
                       LEFT JOIN custos_obra ON custos_obra.obra_id = obras.id
                       WHERE 1 = 1" . $filtro . "
                       GROUP BY obras.id, usuarios.nome
                       ORDER BY obras.id DESC");
$stmt->execute($params);
$relatorioObras = $stmt->fetchAll();

include '../configs/header.php';
?>

<main class="main">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Relatórios <span>Gerais</span></h1>
            <p class="page-subtitle">Resumo consolidado de obras, custos e ocorrências</p>
        </div>
        <div style="display:flex;gap:1rem;align-items:center;">
            <a href="exportar_csv.php" class="btn btn-primary">Exportar CSV</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-icon">🏗️</span>
            <div class="stat-label">Total de Obras</div>
            <div class="stat-value"><?= e($totalObras) ?></div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">✅</span>
            <div class="stat-label">Obras Ativas</div>
            <div class="stat-value"><?= e($obrasAtivas) ?></div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">💰</span>
            <div class="stat-label">Custo Realizado</div>
            <div class="stat-value">R$ <?= number_format($totaisCustos['realizado'], 2, ',', '.') ?></div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">⚠️</span>
            <div class="stat-label">Pendências</div>
            <div class="stat-value"><?= e($ocorrenciasPendentes) ?></div>
        </div>
    </div>

    <div class="section-label">Relatório por Obra</div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Obra</th>
                    <th>Responsável</th>
                    <th>Status</th>
                    <th>Progresso</th>
                    <th>Previsão</th>
                    <th>Custo Realizado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($relatorioObras)): ?>
                    <tr><td colspan="6" class="table-empty">Nenhum dado disponível.</td></tr>
                <?php endif; ?>
                <?php foreach ($relatorioObras as $obra): ?>
                    <tr>
                        <td><?= e($obra['nome']) ?></td>
                        <td><?= e($obra['responsavel'] ?? 'Não definido') ?></td>
                        <td><?= e($obra['status']) ?></td>
                        <td><?= e($obra['progresso_pct']) ?>%</td>
                        <td><?= !empty($obra['data_previsao']) ? date('d/m/Y', strtotime($obra['data_previsao'])) : '-' ?></td>
                        <td>R$ <?= number_format($obra['custo_realizado'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php include '../configs/footer.php'; ?>
