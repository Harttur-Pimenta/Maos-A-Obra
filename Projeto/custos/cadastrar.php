<?php
$current_page = 'custos';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

if (ehEngenheiro()) {
    $stmt = $pdo->prepare('SELECT id, nome FROM obras WHERE responsavel_id = :usuario_id ORDER BY nome ASC');
    $stmt->execute([':usuario_id' => usuarioId()]);
    $obras = $stmt->fetchAll();
} else {
    $obras = $pdo->query('SELECT id, nome FROM obras ORDER BY nome ASC')->fetchAll();
}

$usuarios = $pdo->query('SELECT id, nome FROM usuarios ORDER BY nome ASC')->fetchAll();

include '../configs/header.php';
?>

<main class="main">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Custos e Materiais <span>Cadastrar</span></h1>
            <p class="page-subtitle">Inserir novo gasto</p>
        </div>
    </div>

    <form action="salvar.php" method="POST" class="formulario">
        <label>Obra</label>
        <select name="obra_id" required>
            <option value="">Selecione</option>
            <?php foreach ($obras as $obra): ?>
                <option value="<?= e($obra['id']) ?>"><?= e($obra['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Descrição</label>
        <input type="text" name="descricao" required>

        <label>Tipo</label>
        <select name="tipo" required>
            <option value="material">Material</option>
            <option value="servico">Serviço</option>
            <option value="equipamento">Equipamento</option>
            <option value="outro">Outro</option>
        </select>

        <label>Responsável pelo lançamento</label>
        <select name="usuario_id">
            <option value="">Sistema / não definido</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= e($usuario['id']) ?>" <?= usuarioId() === (int) $usuario['id'] ? 'selected' : '' ?>><?= e($usuario['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Quantidade Planejada</label>
        <input type="number" step="0.001" name="qtd_planejada" value="0">

        <label>Quantidade Realizada</label>
        <input type="number" step="0.001" name="qtd_realizada" value="0">

        <label>Valor Planejado</label>
        <input type="number" step="0.01" name="valor_planejado" value="0">

        <label>Valor Realizado</label>
        <input type="number" step="0.01" name="valor_realizado" value="0">

        <label>Data do Lançamento</label>
        <input type="date" name="data_lancamento" value="<?= date('Y-m-d') ?>" required>

        <button type="submit">Salvar Custo</button>
    </form>
</div>
</main>

<?php include '../configs/footer.php'; ?>
