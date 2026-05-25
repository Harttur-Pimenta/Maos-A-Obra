<?php
require_once '../configs/banco.php';
include '../configs/header.php';

$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY nome ASC");
$usuarios = $stmt->fetchAll();
?>

<h1>Cadastrar Obra</h1>

<form action="salvar.php" method="POST" class="formulario">

    <label>Nome da Obra</label>
    <input type="text" name="nome" required>

    <label>Endereço</label>
    <textarea name="endereco"></textarea>

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
    <input type="date" name="data_inicio">

    <label>Data Prevista</label>
    <input type="date" name="data_previsao">

    <label>Data Final</label>
    <input type="date" name="data_fim">

    <label>Orçamento Total</label>
    <input type="number" step="0.01" name="orcamento_total" value="0">

    <label>Progresso (%)</label>
    <input type="number" name="progresso_pct" min="0" max="100" value="0">

    <button type="submit">Salvar</button>

</form>

</main>
</body>
</html>