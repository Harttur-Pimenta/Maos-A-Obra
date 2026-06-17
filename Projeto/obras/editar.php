<?php
$current_page = 'obras';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

if (!$id || !obraPertenceAoUsuario($pdo, $id)) {
    negarAcesso();
}

$stmt = $pdo->prepare('SELECT * FROM obras WHERE id = :id');
$stmt->execute([':id' => $id]);
$obra = $stmt->fetch();

if (!$obra) {
    die('Obra não encontrada.');
}

if (ehAdmin()) {
    $stmtUsuarios = $pdo->query("SELECT id, nome FROM usuarios WHERE perfil IN ('admin','engenheiro','tecnico') ORDER BY nome ASC");
    $usuarios = $stmtUsuarios->fetchAll();
} else {
    $usuarios = [[
        'id' => usuarioId(),
        'nome' => usuarioNome()
    ]];
}

include '../configs/header.php';
?>

<main class="main">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Obras <span>Editar</span></h1>
            <p class="page-subtitle">Editar dados do projeto selecionado</p>
        </div>
    </div>

    <form action="atualizar.php" method="POST" class="formulario">
        <input type="hidden" name="id" value="<?= e($obra['id']) ?>">

        <label>Nome da Obra</label>
        <input type="text" name="nome" value="<?= e($obra['nome']) ?>" required>

        <label>Endereço</label>
        <textarea name="endereco"><?= e($obra['endereco']) ?></textarea>

        <label>Status</label>
        <select name="status">
            <option value="ativa" <?= $obra['status'] === 'ativa' ? 'selected' : '' ?>>Ativa</option>
            <option value="pausada" <?= $obra['status'] === 'pausada' ? 'selected' : '' ?>>Pausada</option>
            <option value="finalizada" <?= $obra['status'] === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
        </select>

        <label>Responsável</label>
        <select name="responsavel_id" required>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= e($usuario['id']) ?>" <?= (int) $obra['responsavel_id'] === (int) $usuario['id'] ? 'selected' : '' ?>>
                    <?= e($usuario['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Data de Início</label>
        <input type="date" name="data_inicio" value="<?= e($obra['data_inicio']) ?>">

        <label>Data Prevista</label>
        <input type="date" name="data_previsao" value="<?= e($obra['data_previsao']) ?>">

        <label>Data Final</label>
        <input type="date" name="data_fim" value="<?= e($obra['data_fim']) ?>">

        <label>Orçamento Total</label>
        <input type="number" step="0.01" name="orcamento_total" value="<?= e($obra['orcamento_total']) ?>">

        <label>Progresso (%)</label>
        <input type="number" name="progresso_pct" min="0" max="100" value="<?= e($obra['progresso_pct']) ?>">

        <button type="submit">Salvar</button>
    </form>
</div>
</main>

<?php include '../configs/footer.php'; ?>
