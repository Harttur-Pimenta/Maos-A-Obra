<?php
$current_page = 'obras';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$busca = $_GET['busca'] ?? '';

$sql = "SELECT 
            obras.*,
            usuarios.nome AS responsavel
        FROM obras
        LEFT JOIN usuarios ON obras.responsavel_id = usuarios.id
        WHERE 1 = 1";

$params = [];

if (ehEngenheiro()) {
    $sql .= " AND obras.responsavel_id = :usuario_id";
    $params[':usuario_id'] = usuarioId();
}

if (!empty($busca)) {
    $sql .= " AND (
                    obras.nome LIKE :busca
                    OR obras.endereco LIKE :busca
                    OR obras.status LIKE :busca
                    OR usuarios.nome LIKE :busca
                )";
    $params[':busca'] = "%{$busca}%";
}

$sql .= " ORDER BY obras.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$obras = $stmt->fetchAll();

include '../configs/header.php';
?>

<main class="main">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Obras <span>Gerais</span></h1>
            <p class="page-subtitle">Visualizar, inserir e editar projetos</p>
        </div>
        <div style="display:flex;gap:1rem;align-items:center;">
            <form method="GET" class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" name="busca" placeholder="Buscar obra..." value="<?= e($busca) ?>">
            </form>
            <a href="../obras/cadastrar.php" class="btn btn-primary">+ Nova Obra</a>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Status</th>
                    <th>Responsável</th>
                    <th>Início</th>
                    <th>Previsão</th>
                    <th>Encerramento</th>
                    <th>Progresso</th>
                    <th>Orçamento</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($obras)): ?>
                    <tr><td colspan="11" class="table-empty">Nenhuma obra encontrada.</td></tr>
                <?php endif; ?>
                <?php foreach ($obras as $obra): ?>
                    <tr>
                        <td><?= e($obra['id']) ?></td>
                        <td><?= e($obra['nome']) ?></td>
                        <td><?= e($obra['endereco'] ?: '-') ?></td>
                        <td><span class="badge <?= $obra['status'] === 'ativa' ? 'badge-success' : ($obra['status'] === 'pausada' ? 'badge-warning' : 'badge-muted') ?>"><?= e($obra['status']) ?></span></td>
                        <td><?= e($obra['responsavel'] ?? 'Não definido') ?></td>
                        <td><?= !empty($obra['data_inicio']) ? date('d/m/Y', strtotime($obra['data_inicio'])) : '-' ?></td>
                        <td><?= !empty($obra['data_previsao']) ? date('d/m/Y', strtotime($obra['data_previsao'])) : '-' ?></td>
                        <td><?= !empty($obra['data_fim']) ? date('d/m/Y', strtotime($obra['data_fim'])) : '-' ?></td>
                        <td><?= e($obra['progresso_pct']) ?>%</td>
                        <td>R$ <?= number_format($obra['orcamento_total'], 2, ',', '.') ?></td>
                        <td style="display:flex;gap:1rem;align-items:center;">
                            <a href="editar.php?id=<?= e($obra['id']) ?>" class="btn btn-secondary btn-sm">Editar</a>
                            <a href="excluir.php?id=<?= e($obra['id']) ?>" class="btn-delete btn-sm" onclick="return confirm('Deseja realmente apagar esta obra?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php include '../configs/footer.php'; ?>
