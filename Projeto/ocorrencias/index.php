<?php
$current_page = 'ocorrencias';

require_once '../configs/banco.php';
include '../configs/header.php';

$busca = $_GET['busca'] ?? '';

$sql = "SELECT
            ocorrencias.*,
            obras.nome AS obra,
            usuarios.nome AS responsavel
        FROM ocorrencias

        LEFT JOIN obras
            ON ocorrencias.obra_id = obras.id

        LEFT JOIN usuarios
            ON ocorrencias.usuario_id = usuarios.id";

$params = [];

if (!empty($busca)) {

    $sql .= " WHERE (
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
?>

<main class="main">
<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">Ocorrências <span>Gerais</span></h1>
            <p class="page-subtitle">Visualizar, Inserir e Editar Gastos</p>
        </div>
        <div style="display:flex;gap:1rem;align-items:center;">
            <form method="GET" class="search-bar">
                <span class="search-icon">🔍</span>
                <input 
                    type="text"
                    name="busca"
                    placeholder="Buscar ocorrência..."
                    value="<?= htmlspecialchars($busca) ?>"
                >
            </form>
            <a href="../ocorrencias/cadastrar.php" class="btn btn-primary">+ Nova ocorrencia</a>
        </div>
    </div>

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

            <?php foreach ($ocorrencias as $ocorrencia): ?>

                <tr>

                    <td><?= $ocorrencia['id'] ?></td>

                    <td>
                        <?= htmlspecialchars($ocorrencia['obra'] ?? '-') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($ocorrencia['titulo']) ?>
                    </td>

                    <td><?= $ocorrencia['categoria'] ?></td>

                    <td><?= $ocorrencia['status'] ?></td>

                    <td><?= $ocorrencia['prioridade'] ?></td>

                    <td>
                        <?= $ocorrencia['responsavel'] ?? 'Não definido' ?>
                    </td>

                    <td>
                        <?= date('d/m/Y H:i', strtotime($ocorrencia['criado_em'])) ?>
                    </td>

                    <td style="display:flex;gap:1rem;align-items:center;">
                        <a href="editar.php?id=<?= $ocorrencia['id'] ?>" 
                        class="btn btn-secondary btn-sm">
                        Editar
                        </a>
                        <a href="excluir.php?id=<?= $ocorrencia['id'] ?>" 
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