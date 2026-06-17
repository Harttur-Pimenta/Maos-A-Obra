<?php
$current_page = 'custos';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

if (!$id || !custoPertenceAoUsuario($pdo, $id)) {
    negarAcesso();
}

$stmt = $pdo->prepare('SELECT * FROM custos_obra WHERE id = :id');
$stmt->execute([':id' => $id]);
$custo = $stmt->fetch();

if (!$custo) {
    die('Custo não encontrado.');
}

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
            <h1 class="page-title">Custos e Materiais <span>Editar</span></h1>
            <p class="page-subtitle">Editar gasto selecionado</p>
        </div>
    </div>

    <form action="atualizar.php" method="POST" class="formulario">
        <input type="hidden" name="id" value="<?= e($custo['id']) ?>">

        <label>Obra</label>
        <select name="obra_id" required>
            <?php foreach ($obras as $obra): ?>
                <option value="<?= e($obra['id']) ?>" <?= (int) $custo['obra_id'] === (int) $obra['id'] ? 'selected' : '' ?>>
                    <?= e($obra['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Descrição</label>
        <input type="text" name="descricao" value="<?= e($custo['descricao']) ?>" required>

        <label>Tipo</label>
        <select name="tipo" required>
            <option value="material" <?= $custo['tipo'] === 'material' ? 'selected' : '' ?>>Material</option>
            <option value="servico" <?= $custo['tipo'] === 'servico' ? 'selected' : '' ?>>Serviço</option>
            <option value="equipamento" <?= $custo['tipo'] === 'equipamento' ? 'selected' : '' ?>>Equipamento</option>
            <option value="outro" <?= $custo['tipo'] === 'outro' ? 'selected' : '' ?>>Outro</option>
        </select>

        <label>Responsável pelo lançamento</label>
        <select name="usuario_id">
            <option value="">Sistema / não definido</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= e($usuario['id']) ?>" <?= (int) $custo['usuario_id'] === (int) $usuario['id'] ? 'selected' : '' ?>>
                    <?= e($usuario['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Quantidade Planejada</label>
        <input type="number" step="0.001" name="qtd_planejada" value="<?= e($custo['qtd_planejada']) ?>">

        <label>Quantidade Realizada</label>
        <input type="number" step="0.001" name="qtd_realizada" value="<?= e($custo['qtd_realizada']) ?>">

        <label>Valor Planejado</label>
        <input type="number" step="0.01" name="valor_planejado" value="<?= e($custo['valor_planejado']) ?>">

        <label>Valor Realizado</label>
        <input type="number" step="0.01" name="valor_realizado" value="<?= e($custo['valor_realizado']) ?>">

        <label>Data do Lançamento</label>
        <input type="date" name="data_lancamento" value="<?= e($custo['data_lancamento']) ?>">

        <button type="submit">Salvar</button>
    </form>
</div>
</main>

<?php include '../configs/footer.php'; ?>
