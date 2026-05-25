<?php
$current_page = 'dashboard';

/* Importa os dados */
require_once '../configs/banco.php';

$totalObras = $pdo->query("SELECT COUNT(*) FROM obras")->fetchColumn();

$custoTotal = $pdo->query("SELECT SUM(valor_realizado) FROM custos_obra")->fetchColumn();

$obrasFinalizadas = $pdo->query("SELECT COUNT(*) FROM obras WHERE status = 'finalizada'")->fetchColumn();

$custoTotal = $custoTotal ?: 0;

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

    <div class="alert alert-warning">
        ⚠️ <strong>2 obras</strong> com prazo próximo ao vencimento nos próximos 7 dias.
        <a href="../obras/index.php" style="color:inherit;text-decoration:underline;">Ver obras</a>
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
            <div class="stat-value"><?= $custoTotal ?></div>

        </div>
        <div class="stat-card">
            <span class="stat-icon">⚠️</span>
            <div class="stat-label">Ocorrências Abertas</div>
            <div class="stat-value">7</div>
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
                        <?php
                        $obras = [
                            ['nome'=>'Residencial Jardins',       'cidade'=>'Uberlândia, MG', 'resp'=>'João Silva',   'pct'=>68, 'prazo'=>'15/08/2025', 'status'=>'badge-success', 'label'=>'Em dia'],
                            ['nome'=>'Edifício Comercial Centro',  'cidade'=>'Uberlândia, MG', 'resp'=>'Maria Costa',  'pct'=>41, 'prazo'=>'30/06/2025', 'status'=>'badge-warning', 'label'=>'Atenção'],
                            ['nome'=>'Reforma Escolar Estadual',   'cidade'=>'Araguari, MG',   'resp'=>'Carlos Melo',  'pct'=>89, 'prazo'=>'10/06/2025', 'status'=>'badge-success', 'label'=>'Em dia'],
                            ['nome'=>'Ponte Rio Uberabinha',        'cidade'=>'Uberlândia, MG', 'resp'=>'Ana Ferreira', 'pct'=>23, 'prazo'=>'20/03/2026', 'status'=>'badge-danger',  'label'=>'Atrasada'],
                            ['nome'=>'Condomínio Solar Norte',     'cidade'=>'Uberaba, MG',    'resp'=>'Pedro Rocha',  'pct'=>55, 'prazo'=>'01/11/2025', 'status'=>'badge-success', 'label'=>'Em dia'],
                        ];
                        foreach ($obras as $o):
                            $prog_class = $o['pct'] >= 70 ? 'success' : ($o['label'] === 'Atrasada' ? 'danger' : '');
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($o['nome']) ?></strong><br>
                                <small style="color:var(--muted)"><?= htmlspecialchars($o['cidade']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($o['resp']) ?></td>
                            <td>
                                <span style="font-size:1.2rem"><?= $o['pct'] ?>%</span>
                                <div class="progress-bar">
                                    <div class="progress-fill <?= $prog_class ?>" style="width:<?= $o['pct'] ?>%"></div>
                                </div>
                            </td>
                            <td class="mono"><?= $o['prazo'] ?></td>
                            <td><span class="badge <?= $o['status'] ?>"><?= $o['label'] ?></span></td>
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
                    <?php
                    $atividades = [
                        ['icon'=>'📋', 'data'=>'Hoje, 09:14',   'texto'=>'Diário registrado — Residencial Jardins'],
                        ['icon'=>'⚠️', 'data'=>'Hoje, 08:30',   'texto'=>'Ocorrência aberta — Atraso entrega de aço'],
                        ['icon'=>'💰', 'data'=>'Ontem, 17:22',  'texto'=>'Custo lançado — R$ 18.400 em cimento'],
                        ['icon'=>'✅', 'data'=>'Ontem, 15:00',  'texto'=>'Ocorrência encerrada — Vazamento resolvido'],
                        ['icon'=>'📄', 'data'=>'22/05, 11:08',  'texto'=>'Relatório gerado — Reforma Escolar (Mensal)'],
                    ];
                    foreach ($atividades as $a): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"><?= $a['icon'] ?></div>
                        <div class="timeline-content">
                            <div class="timeline-date"><?= $a['data'] ?></div>
                            <div class="timeline-text"><?= htmlspecialchars($a['texto']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top:1.6rem;text-align:center;">
                    <a href="../diario/index.php" style="font-size:1.2rem;color:var(--amber)">Ver todo o histórico →</a>
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

            <div class="section-label" style="margin-top:2rem;">Atalhos Rápidos</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem;">
                <a href="../obras/cadastrar.php" class="btn btn-secondary" style="justify-content:center;">🏗️ Nova Obra</a>
                <a href="../diario/index.php"    class="btn btn-secondary" style="justify-content:center;">📋 Novo Diário</a>
                <a href="../custos/index.php"    class="btn btn-secondary" style="justify-content:center;">💰 Lançar Custo</a>
                <a href="../ocorrencias/index.php" class="btn btn-secondary" style="justify-content:center;">⚠️ Ocorrência</a>
            </div>

        </div>
    </div>

</div>
</main>

<?php include '../configs/footer.php'; ?>