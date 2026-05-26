<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = 'dashboard';

require_once '../configs/banco.php';

$params = [];
$filtroEngenheiro = "";

if ($_SESSION['usuario_perfil'] == 'engenheiro') {
    $filtroEngenheiro = " WHERE obras.responsavel_id = :usuario_id";
    $params[':usuario_id'] = $_SESSION['usuario_id'];
}

$sqlTotalObras = "SELECT COUNT(*) FROM obras" . $filtroEngenheiro;
$stmt = $pdo->prepare($sqlTotalObras);
$stmt->execute($params);
$totalObras = $stmt->fetchColumn();

$sqlTotalOcorrencias = "SELECT COUNT(*) 
                        FROM ocorrencias
                        INNER JOIN obras ON ocorrencias.obra_id = obras.id"
                        . $filtroEngenheiro;

$stmt = $pdo->prepare($sqlTotalOcorrencias);
$stmt->execute($params);
$totalOcorrencias = $stmt->fetchColumn();

$sqlCustoTotal = "SELECT COALESCE(SUM(custos_obra.valor_realizado), 0)
                  FROM custos_obra
                  INNER JOIN obras ON custos_obra.obra_id = obras.id"
                  . $filtroEngenheiro;

$stmt = $pdo->prepare($sqlCustoTotal);
$stmt->execute($params);
$custoTotal = $stmt->fetchColumn();

$sqlObrasFinalizadas = "SELECT COUNT(*) 
                        FROM obras"
                        . $filtroEngenheiro .
                        ($_SESSION['usuario_perfil'] == 'engenheiro' ? " AND" : " WHERE") .
                        " obras.status = 'finalizada'";

$stmt = $pdo->prepare($sqlObrasFinalizadas);
$stmt->execute($params);
$obrasFinalizadas = $stmt->fetchColumn();

$sqlObras = "SELECT 
                obras.*,
                usuarios.nome AS responsavel
            FROM obras
            LEFT JOIN usuarios 
                ON obras.responsavel_id = usuarios.id
            WHERE obras.status != 'finalizada'";

if ($_SESSION['usuario_perfil'] == 'engenheiro') {
    $sqlObras .= " AND obras.responsavel_id = :usuario_id";
}

$sqlObras .= " ORDER BY obras.id DESC LIMIT 5";

$stmt = $pdo->prepare($sqlObras);
$stmt->execute($params);
$obrasAndamento = $stmt->fetchAll();

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
            <!--<div class="stat-meta"><span class="up">↑ 3</span> neste mês</div>-->
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
            <!--<div class="stat-meta"><span class="down">↓ 2</span> resolvidas hoje</div>-->
        </div>
        <div class="stat-card">
            <span class="stat-icon">✅</span>
            <div class="stat-label">Obras Concluídas</div>
            <div class="stat-value"><?= $obrasFinalizadas ?></div>
             <!--<div class="stat-meta">Total histórico</div>-->
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

                            <td>
                                <?= htmlspecialchars($obra['responsavel'] ?? 'Não definido') ?>
                            </td>

                            <td>

                                <span style="font-size:1.2rem">
                                    <?= $progresso ?>%
                                </span>

                                <div class="progress-bar">
                                    <div 
                                        class="progress-fill <?= $prog_class ?>" 
                                        style="width:<?= $progresso ?>%"
                                    ></div>
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
                    </tbody>
                </table>
            </div>
            <div style="text-align:right;margin-top:1rem;">
                <a href="../obras/index.php" style="font-size:1.2rem;color:var(--amber)">Ver todas as obras →</a>
            </div>
        </div>

        <div>

            <div class="section-label">Atividade Recente</div>

            <div class="sidebar-card">

                <div class="timeline">

                    <?php foreach ($historicos as $h): ?>

                        <div class="timeline-item">

                            <div class="timeline-dot">
                                <?php if ($h['tipo'] == 'custo'): ?> 💰 <?php else: ?> ⚠️ <?php endif; ?>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-date">
                                    <?= date('d/m/Y H:i', strtotime($h['criado_em'])) ?>
                                </div>
                                <div class="timeline-text">
                                    <strong><?= htmlspecialchars($h['acao']) ?></strong><br>
                                    <?= htmlspecialchars($h['descricao']) ?><br>

                                    <small style="color:var(--muted)">
                                        <?= $h['usuario'] ?? 'Sistema' ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
                <div style="margin-top:1.6rem;text-align:center;">
                    <a href="../custos/index.php" style="font-size:1.2rem;color:var(--amber)">
                        Ver todo o histórico →
                    </a>
                </div>
            </div>

            <div class="sidebar-card" style="margin-top:1.6rem;">
                <div class="card-title" style="margin-bottom:1.6rem;">
                    <div class="icon">💰</div> Custo por Categoria
                </div>
                <?php
                $categorias = [
                    ['label'=>'Mão de Obra',   'pct'=>42, 'cor'=>'var(--amber)'],
                    ['label'=>'Materiais',     'pct'=>31, 'cor'=>'var(--info)'],
                    ['label'=>'Equipamentos',  'pct'=>17, 'cor'=>'var(--success)'],
                    ['label'=>'Serviços ext.', 'pct'=>10, 'cor'=>'var(--danger)'],
                ];
                foreach ($categorias as $c): ?>
                <div style="margin-bottom:1.2rem;">
                    <div style="display:flex;justify-content:space-between;font-size:1.2rem;margin-bottom:.4rem;">
                        <span style="color:var(--muted)"><?= $c['label'] ?></span>
                        <span class="mono"><?= $c['pct'] ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:<?= $c['pct'] ?>%;background:<?= $c['cor'] ?>"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <div style="margin-top:1.4rem;text-align:right;">
                    <a href="../custos/index.php" style="font-size:1.2rem;color:var(--amber)">Ver custos detalhados →</a>
                </div>
            </div>

        </div>
    </div>

</div>
</main>

<?php include '../configs/footer.php'; ?>