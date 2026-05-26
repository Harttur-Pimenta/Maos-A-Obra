<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = 'obras';

require_once '../configs/banco.php';
include '../configs/header.php';

$busca = $_GET['busca'] ?? '';

$sql = "SELECT 
            obras.*,
            usuarios.nome AS responsavel
        FROM obras

        LEFT JOIN usuarios 
            ON obras.responsavel_id = usuarios.id

        WHERE 1 = 1";

$params = [];

/* Se for engenheiro, mostra só as obras dele */
if ($_SESSION['usuario_perfil'] == 'engenheiro') {
    $sql .= " AND obras.responsavel_id = :usuario_id";
    $params[':usuario_id'] = $_SESSION['usuario_id'];
}

/* Busca */
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
?>

<main class="main">
<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">Obras <span>Gerais</span></h1>
            <p class="page-subtitle">Visualizar, Inserir e Editar Projetos</p>
        </div>
        <div style="display:flex;gap:1rem;align-items:center;">
            <form method="GET" class="search-bar">
                <span class="search-icon">🔍</span>
                <input 
                    type="text" 
                    name="busca"
                    placeholder="Buscar obra..."
                    value="<?= $_GET['busca'] ?? '' ?>"
                >
            </form>
            <a href="../obras/cadastrar.php" class="btn btn-primary">+ Nova Obra</a>
        </div>
    </div>

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
            <?php foreach ($obras as $obra): ?>
                <tr>
                    <td><?= $obra['id'] ?></td>
                    <td><?= htmlspecialchars($obra['nome']) ?></td>
                    <td><?= $obra['endereco'] ?></td>
                    <td><?= $obra['status'] ?></td>
                    <td><?= $obra['responsavel'] ?? 'Não definido' ?></td>
                    <td><?= $obra['data_inicio'] ?></td>
                    <td><?= $obra['data_previsao'] ?></td>
                    <td><?= $obra['data_fim'] ?></td>
                    <td><?= $obra['progresso_pct'] ?></td>
                    <td>R$ <?= number_format($obra['orcamento_total'], 2, ',', '.') ?></td>
                    <td style="display:flex;gap:1rem;align-items:center;">
                        <a href="editar.php?id=<?= $obra['id'] ?>" 
                        class="btn btn-secondary btn-sm">
                        Editar
                        </a>
                        <a href="excluir.php?id=<?= $obra['id'] ?>" 
                        class="btn-delete btn-sm"
                        onclick="return confirm('Deseja realmente apagar esta obra?')">
                        Excluir
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
</main>
</body>
</html>