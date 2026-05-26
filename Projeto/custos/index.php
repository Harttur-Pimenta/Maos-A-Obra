<?php
$current_page = 'custos';

require_once '../configs/banco.php';
include '../configs/header.php';

$busca = $_GET['busca'] ?? '';

$sql = "SELECT
            custos_obra.*,
            obras.nome AS obra,
            usuarios.nome AS responsavel
        FROM custos_obra

        LEFT JOIN obras
            ON custos_obra.obra_id = obras.id

        LEFT JOIN usuarios
            ON custos_obra.usuario_id = usuarios.id";

$params = [];

if (!empty($busca)) {
    $sql .= " WHERE (
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
?>

<main class="main">
<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">Custos e Materiais <span>Gerais</span></h1>
            <p class="page-subtitle">Visualizar, Inserir e Editar Gastos</p>
        </div>
        <div style="display:flex;gap:1rem;align-items:center;">
            <form method="GET" class="search-bar">
                <span class="search-icon">🔍</span>

                <input 
                    type="text"
                    name="busca"
                    placeholder="Buscar custo, material, obra..."
                    value="<?= htmlspecialchars($busca) ?>"
                >
            </form>
            <a href="../custos/cadastrar.php" class="btn btn-primary">+ Novo custo</a>
        </div>
    </div>

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

            <?php foreach ($custos as $custo): ?>

                <tr>
                    <td><?= $custo['id'] ?></td>
                    <td><?= htmlspecialchars($custo['obra'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($custo['descricao']) ?></td>
                    <td><?= $custo['tipo'] ?></td>
                    <td><?= $custo['responsavel'] ?? 'Não definido' ?></td>
                    <td><?= $custo['qtd_planejada'] ?></td>
                    <td><?= $custo['qtd_realizada'] ?></td>
                    <td>R$ <?= number_format($custo['valor_planejado'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($custo['valor_realizado'], 2, ',', '.') ?></td>
                    <td><?= $custo['data_lancamento'] ?></td>
                    <td style="display:flex;gap:1rem;align-items:center;">

    <a href="editar.php?id=<?= $custo['id'] ?>" 
       class="btn btn-secondary btn-sm">
       Editar
    </a>

    <a href="delete.php?id=<?= $custo['id'] ?>" 
       class="btn-delete btn-sm"
       onclick="return confirm('Deseja realmente excluir este custo?')">
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