<?php
require_once '../configs/banco.php';
include '../configs/header.php';

$sql = "SELECT 
            obras.*,
            usuarios.nome AS responsavel
        FROM obras
        LEFT JOIN usuarios ON obras.responsavel_id = usuarios.id
        ORDER BY obras.id DESC";

$stmt = $pdo->query($sql);
$obras = $stmt->fetchAll();
?>

<h1>Obras</h1>

<a class="btn" href="cadastrar.php">Cadastrar Obra</a>

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
                <td>
                    <a href="editar.php?id=<?= $obra['id'] ?>">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</main>
</body>
</html>