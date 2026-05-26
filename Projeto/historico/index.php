<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = 'historico';

require_once '../configs/banco.php';
include '../configs/header.php';

$sql = "SELECT
            historico.*,
            usuarios.nome AS usuario
        FROM historico

        LEFT JOIN usuarios
            ON historico.usuario_id = usuarios.id

        WHERE 1 = 1";

$params = [];

/* Engenheiro vê apenas histórico das obras dele */
if ($_SESSION['usuario_perfil'] == 'engenheiro') {

    $sql .= " AND (
                    (
                        historico.tipo = 'custo'
                        AND historico.referencia_id IN (
                            SELECT custos_obra.id
                            FROM custos_obra
                            INNER JOIN obras
                                ON custos_obra.obra_id = obras.id
                            WHERE obras.responsavel_id = :usuario_id
                        )
                    )

                    OR

                    (
                        historico.tipo = 'ocorrencia'
                        AND historico.referencia_id IN (
                            SELECT ocorrencias.id
                            FROM ocorrencias
                            INNER JOIN obras
                                ON ocorrencias.obra_id = obras.id
                            WHERE obras.responsavel_id = :usuario_id
                        )
                    )
                )";

    $params[':usuario_id'] = $_SESSION['usuario_id'];
}

$sql .= " ORDER BY historico.criado_em DESC";

$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$historicos = $stmt->fetchAll();
?>

<main class="main">
<div class="container">

    <h1>Histórico do Sistema</h1>

    <table>

        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Ação</th>
                <th>Descrição</th>
                <th>Usuário</th>
                <th>Data</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($historicos as $h): ?>

                <tr>

                    <td>
                        <?= $h['id'] ?>
                    </td>

                    <td>

                        <?php if ($h['tipo'] == 'custo'): ?>

                            💰 Custo

                        <?php else: ?>

                            ⚠️ Ocorrência

                        <?php endif; ?>

                    </td>

                    <td>
                        <?= htmlspecialchars($h['acao']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($h['descricao']) ?>

                        <?php if (!empty($h['status_novo'])): ?>
                            <br>
                            <small style="color:orange;">
                                Status:
                                <?= $h['status_novo'] ?>
                            </small>
                        <?php endif; ?>
                    </td>


                    <td>
                        <?= htmlspecialchars($h['usuario'] ?? 'Sistema') ?>
                    </td>

                    <td>
                        <?= date('d/m/Y H:i', strtotime($h['criado_em'])) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>
</main>

</body>
</html>