<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = 'obras';
require_once '../configs/banco.php';
include '../configs/header.php';

$id = $_GET['id'] ?? null;

$sql = "SELECT * FROM obras WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $id
]);

$obra = $stmt->fetch();

$stmtUsuarios = $pdo->query("SELECT id, nome FROM usuarios ORDER BY nome ASC");
$usuarios = $stmtUsuarios->fetchAll();
?>

<main class="main">
<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">Obras <span>Editar</span></h1>
            <p class="page-subtitle">Editar Dados do Projeto Selecionado</p>
        </div>
    </div>

    <form action="atualizar.php" method="POST" class="formulario">

        <!-- input para o ID usado na consulta -->
        <input type="hidden" name="id" value="<?= $obra['id'] ?>">

        <label>Nome da Obra</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($obra['nome']) ?>" required>
        
        <label>Endereço</label>
        <textarea name="endereco"><?= htmlspecialchars($obra['endereco']) ?></textarea>

        <label>Status</label>
        <select name="status">
            <option value="ativa">Ativa</option>
            <option value="pausada">Pausada</option>
            <option value="finalizada">Finalizada</option>
        </select>

        <label>Responsável</label>
        <select name="responsavel_id">
            <option value="">Selecione</option>

            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario['id'] ?>">
                    <?= htmlspecialchars($usuario['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Data de Início</label>
        <input type="date" name="data_inicio" value="<?= htmlspecialchars($obra['data_inicio']) ?>" required>

        <label>Data Prevista</label>
        <input type="date" name="data_previsao" value="<?= htmlspecialchars($obra['data_previsao']) ?>" required>

        <label>Data Final</label>
        <input type="date" name="data_fim" value="<?= htmlspecialchars($obra['data_fim']) ?>" >

        <label>Orçamento Total</label>
        <input type="number" step="0.01" name="orcamento_total" value="<?= $obra['orcamento_total'] ?>">

        <label>Progresso (%)</label>
        <input type="number" name="progresso_pct" min="0" max="100" value="<?= $obra['progresso_pct'] ?>">

        <button type="submit">Salvar</button>
    </form>

</div>
</main>
</body>