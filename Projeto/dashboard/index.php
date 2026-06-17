<?php
$current_page = 'dashboard';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$paramsObras = [];
$filtroObras = "";

if (ehEngenheiro()) {
    $filtroObras = " AND obras.responsavel_id = :usuario_id";
    $paramsObras[':usuario_id'] = usuarioId();
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM obras WHERE status = 'ativa'" . $filtroObras);
$stmt->execute($paramsObras);
$totalObras = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*)
                       FROM ocorrencias
                       INNER JOIN obras ON ocorrencias.obra_id = obras.id
                       WHERE ocorrencias.status = 'aberta'" . $filtroObras);
$stmt->execute($paramsObras);
$totalOcorrencias = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(custos_obra.valor_realizado), 0)
                       FROM custos_obra
                       INNER JOIN obras ON custos_obra.obra_id = obras.id
                       WHERE 1 = 1" . $filtroObras);
$stmt->execute($paramsObras);
$custoTotal = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM obras WHERE status = 'finalizada'" . $filtroObras);
$stmt->execute($paramsObras);
$obrasFinalizadas = $stmt->fetchColumn();

$sqlObras = "SELECT 
                obras.*,
                usuarios.nome AS responsavel
            FROM obras
            LEFT JOIN usuarios ON obras.responsavel_id = usuarios.id
            WHERE obras.status != 'finalizada'" . $filtroObras . "
            ORDER BY obras.id DESC
            LIMIT 5";
$stmt = $pdo->prepare($sqlObras);
$stmt->execute($paramsObras);
$obrasAndamento = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlHistorico = "SELECT
                    historico.*,
                    usuarios.nome AS usuario
                 FROM historico
                 LEFT JOIN usuarios ON historico.usuario_id = usuarios.id
                 WHERE 1 = 1";
$paramsHistorico = [];

if (ehEngenheiro()) {
    $sqlHistorico .= " AND (
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
    $paramsHistorico[':usuario_id'] = usuarioId();
}

$sqlHistorico .= " ORDER BY historico.criado_em DESC LIMIT 5";
$stmt = $pdo->prepare($sqlHistorico);
$stmt->execute($paramsHistorico);
$historicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlCategorias = "SELECT 
                    custos_obra.tipo,
                    SUM(custos_obra.valor_realizado) AS total
                  FROM custos_obra
                  INNER JOIN obras ON custos_obra.obra_id = obras.id
                  WHERE 1 = 1" . $filtroObras . "
                  GROUP BY custos_obra.tipo";
$stmt = $pdo->prepare($sqlCategorias);
$stmt->execute($paramsObras);
$dadosCategorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalCategorias = array_sum(array_column($dadosCategorias, 'total'));

$cores = [
    'material' => 'var(--info)',
    'equipamento' => 'var(--success)',
    'servico' => 'var(--amber)',
    'outro' => 'var(--danger)',
];

$nomes = [
    'material' => 'Materiais',
    'equipamento' => 'Equipamentos',
    'servico' => 'Serviços',
    'outro' => 'Outros',
];

$categorias = [];
foreach ($dadosCategorias as $item) {
    $pct = $totalCategorias > 0 ? round(($item['total'] / $totalCategorias) * 100) : 0;
    $tipo = $item['tipo'];
    $categorias[] = [
        'label' => $nomes[$tipo] ?? ucfirst($tipo),
        'pct' => $pct,
        'total' => $item['total'],
        'cor' => $cores[$tipo] ?? 'var(--muted)'
    ];
}

include '../configs/header.php';
?>
<main class="main">
<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard <span>Geral</span></h1>
            <p class="page-subtitle">Visão geral de todas as obras em andamento</p>
        </div>

        <div style="display:flex;gap:1rem;align-items:center;">
            <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" placeholder="Buscar obra, evento...">
            </div>
            <a href="../obras/cadastrar.php" class="btn btn-primary">+ Nova Obra</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-icon">🏗️</span>
            <div class="stat-label">Obras Ativas</div>
            <div class="stat-value"><?= $totalObras ?></div>
        </div>

        <div class="stat-card">
            <span class="stat-icon">💰</span>
            <div class="stat-label">Custo Total (R$)</div>
            <div class="stat-value"><?= 'R$ ' . number_format($custoTotal, 2, ',', '.') ?></div>
        </div>

        <div class="stat-card">
            <span class="stat-icon">⚠️</span>
            <div class="stat-label">Ocorrências Abertas</div>
            <div class="stat-value"><?= $totalOcorrencias ?></div>
        </div>

        <div class="stat-card">
            <span class="stat-icon">✅</span>
            <div class="stat-label">Obras Concluídas</div>
            <div class="stat-value"><?= $obrasFinalizadas ?></div>
        </div>
    </div>

    <div class="grid-2-1">

        <div>
            <div class="section-label">Obras em Andamento</div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Obra</th>
                            <th>Responsável</th>
                            <th>Progresso</th>
                            <th>Prazo</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!empty($obrasAndamento)): ?>
                        <?php foreach ($obrasAndamento as $obra): ?>
                            <?php
                            $progresso = $obra['progresso_pct'] ?? 0;

                            if ($progresso >= 70) {
                                $prog_class = 'success';
                            } elseif ($progresso >= 40) {
                                $prog_class = '';
                            } else {
                                $prog_class = 'danger';
                            }

                            $status = $obra['status'];

                            if ($status == 'ativa') {
                                $badge = 'badge-success';
                                $label = 'Ativa';
                            } else {
                                $badge = 'badge-warning';
                                $label = 'Pausada';
                            }

                            $prazoTexto = 'Não definida';
                            $prazoCor = 'var(--muted)';

                            if (!empty($obra['data_previsao'])) {
                                $hoje = new DateTime();
                                $previsao = new DateTime($obra['data_previsao']);
                                $dias = (int)$hoje->diff($previsao)->format('%r%a');

                                $prazoTexto = abs($dias) . ' dias';

                                if ($dias < 15) {
                                    $prazoCor = 'var(--danger)';
                                } elseif ($dias <= 30) {
                                    $prazoCor = 'var(--amber)';
                                } else {
                                    $prazoCor = 'var(--success)';
                                }
                            }
                            ?>

                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($obra['nome']) ?></strong><br>
                                    <small style="color:var(--muted)">
                                        <?= htmlspecialchars($obra['endereco'] ?? 'Sem endereço') ?>
                                    </small>
                                </td>

                                <td><?= htmlspecialchars($obra['responsavel'] ?? 'Não definido') ?></td>

                                <td>
                                    <span style="font-size:1.2rem"><?= $progresso ?>%</span>
                                    <div class="progress-bar">
                                        <div class="progress-fill <?= $prog_class ?>" style="width:<?= $progresso ?>%"></div>
                                    </div>
                                </td>

                                <td class="mono" style="color:<?= $prazoCor ?>;">
                                    <?= $prazoTexto ?>
                                </td>

                                <td>
                                    <span class="badge <?= $badge ?>">
                                        <?= $label ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="color:var(--muted);text-align:center;">
                                Nenhuma obra em andamento encontrada.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div style="text-align:right;margin-top:1rem;">
                <a href="../obras/index.php" style="font-size:1.2rem;color:var(--amber)">
                    Ver todas as obras →
                </a>
            </div>
        </div>

        <div>

            <div class="section-label">Atividade Recente</div>

            <div class="sidebar-card">
                <div class="timeline">

                    <?php if (!empty($historicos)): ?>
                        <?php foreach ($historicos as $h): ?>

                            <div class="timeline-item">
                                <div class="timeline-dot">
                                    <?= ($h['tipo'] == 'custo') ? '💰' : '⚠️' ?>
                                </div>

                                <div class="timeline-content">
                                    <div class="timeline-date">
                                        <?= date('d/m/Y H:i', strtotime($h['criado_em'])) ?>
                                    </div>

                                    <div class="timeline-text">
                                        <strong><?= htmlspecialchars($h['acao']) ?></strong><br>
                                        <?= htmlspecialchars($h['descricao']) ?><br>

                                        <small style="color:var(--muted)">
                                            <?= htmlspecialchars($h['usuario'] ?? 'Sistema') ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>

                        <p style="color:var(--muted);font-size:1.2rem;">
                            Nenhuma atividade recente encontrada.
                        </p>

                    <?php endif; ?>

                </div>

                <div style="margin-top:1.6rem;text-align:center;">
                    <a href="../historico/index.php" style="font-size:1.2rem;color:var(--amber)">
                        Ver todo o histórico →
                    </a>
                </div>
            </div>

            <div class="sidebar-card" style="margin-top:1.6rem;">
                <div class="card-title" style="margin-bottom:1.6rem;">
                    <div class="icon">💰</div> Custo por Categoria
                </div>

                <?php if (!empty($categorias)): ?>

                    <?php foreach ($categorias as $c): ?>
                        <div style="margin-bottom:1.2rem;">
                            <div style="display:flex;justify-content:space-between;font-size:1.2rem;margin-bottom:.4rem;">
                                <span style="color:var(--muted)">
                                    <?= htmlspecialchars($c['label']) ?>
                                </span>

                                <span class="mono">
                                    <?= $c['pct'] ?>%
                                </span>
                            </div>

                            <div class="progress-bar">
                                <div class="progress-fill" style="width:<?= $c['pct'] ?>%;background:<?= $c['cor'] ?>"></div>
                            </div>

                            <small style="color:var(--muted);font-size:1.1rem;">
                                R$ <?= number_format($c['total'], 2, ',', '.') ?>
                            </small>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>

                    <p style="color:var(--muted);font-size:1.2rem;">
                        Nenhum custo cadastrado.
                    </p>

                <?php endif; ?>

                <div style="margin-top:1.4rem;text-align:right;">
                    <a href="../custos/index.php" style="font-size:1.2rem;color:var(--amber)">
                        Ver custos detalhados →
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
</main>

<?php include '../configs/footer.php'; ?>