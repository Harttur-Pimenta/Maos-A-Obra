<?php
$current_page = 'ocorrencias';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$busca = $_GET['busca'] ?? '';

$sql = "SELECT
            ocorrencias.*,
            obras.nome AS obra,
            usuarios.nome AS responsavel
        FROM ocorrencias
        LEFT JOIN obras ON ocorrencias.obra_id = obras.id
        LEFT JOIN usuarios ON ocorrencias.usuario_id = usuarios.id
        WHERE 1 = 1";

$params = [];

if (ehEngenheiro()) {
    $sql .= " AND obras.responsavel_id = :usuario_id";
    $params[':usuario_id'] = usuarioId();
}

if (!empty($busca)) {
    $sql .= " AND (
                    ocorrencias.titulo LIKE :busca
                    OR ocorrencias.categoria LIKE :busca
                    OR ocorrencias.status LIKE :busca
                    OR ocorrencias.prioridade LIKE :busca
                    OR obras.nome LIKE :busca
                    OR usuarios.nome LIKE :busca
                )";
    $params[':busca'] = "%{$busca}%";
}

$sql .= " ORDER BY ocorrencias.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ocorrencias = $stmt->fetchAll();

include '../configs/header.php';
?>

<main class="main">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Ocorrências <span>Gerais</span></h1>
            <p class="page-subtitle">Visualizar, inserir e editar ocorrências</p>
        </div>
        <div style="display:flex;gap:1rem;align-items:center;">
            <form method="GET" class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" name="busca" placeholder="Buscar ocorrência..." value="<?= e($busca) ?>">
            </form>
            <a href="../ocorrencias/cadastrar.php" class="btn btn-primary">+ Nova Ocorrência</a>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Obra</th>
                    <th>Título</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th>Prioridade</th>
                    <th>Responsável</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ocorrencias)): ?>
                    <tr><td colspan="9" class="table-empty">Nenhuma ocorrência encontrada.</td></tr>
                <?php endif; ?>
                <?php foreach ($ocorrencias as $ocorrencia): ?>
                    <tr>
                        <td><?= e($ocorrencia['id']) ?></td>
                        <td><?= e($ocorrencia['obra'] ?? '-') ?></td>
                        <td><?= e($ocorrencia['titulo']) ?></td>
                        <td><?= e($ocorrencia['categoria']) ?></td>
                        <td><span class="badge <?= $ocorrencia['status'] === 'resolvida' ? 'badge-success' : ($ocorrencia['status'] === 'em_analise' ? 'badge-warning' : 'badge-danger') ?>"><?= e($ocorrencia['status']) ?></span></td>
                        <td><?= e($ocorrencia['prioridade']) ?></td>
                        <td><?= e($ocorrencia['responsavel'] ?? 'Não definido') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($ocorrencia['criado_em'])) ?></td>
                        <td style="display:flex;gap:1rem;align-items:center;">
                            <a href="editar.php?id=<?= e($ocorrencia['id']) ?>" class="btn btn-secondary btn-sm">Editar</a>
                            <a href="excluir.php?id=<?= e($ocorrencia['id']) ?>" class="btn-delete btn-sm" onclick="return confirm('Deseja realmente apagar esta ocorrência?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<?php include '../configs/footer.php'; ?>
