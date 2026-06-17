<?php
$current_page = 'ocorrencias';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

if (!$id || !ocorrenciaPertenceAoUsuario($pdo, $id)) {
    negarAcesso();
}

$stmt = $pdo->prepare('SELECT * FROM ocorrencias WHERE id = :id');
$stmt->execute([':id' => $id]);
$ocorrencia = $stmt->fetch();

if (!$ocorrencia) {
    die('Ocorrência não encontrada.');
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
            <h1 class="page-title">Ocorrências <span>Editar</span></h1>
            <p class="page-subtitle">Editar ocorrência selecionada</p>
        </div>
    </div>

    <form action="atualizar.php" method="POST" class="formulario">
        <input type="hidden" name="id" value="<?= e($ocorrencia['id']) ?>">

        <label>Obra</label>
        <select name="obra_id" required>
            <?php foreach ($obras as $obra): ?>
                <option value="<?= e($obra['id']) ?>" <?= (int) $ocorrencia['obra_id'] === (int) $obra['id'] ? 'selected' : '' ?>>
                    <?= e($obra['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Responsável pelo registro</label>
        <select name="usuario_id">
            <option value="">Sistema / não definido</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= e($usuario['id']) ?>" <?= (int) $ocorrencia['usuario_id'] === (int) $usuario['id'] ? 'selected' : '' ?>>
                    <?= e($usuario['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Título</label>
        <input type="text" name="titulo" value="<?= e($ocorrencia['titulo']) ?>" required>

        <label>Descrição</label>
        <textarea name="descricao"><?= e($ocorrencia['descricao']) ?></textarea>

        <label>Categoria</label>
        <select name="categoria">
            <option value="segurança" <?= $ocorrencia['categoria'] === 'segurança' ? 'selected' : '' ?>>Segurança</option>
            <option value="qualidade" <?= $ocorrencia['categoria'] === 'qualidade' ? 'selected' : '' ?>>Qualidade</option>
            <option value="prazo" <?= $ocorrencia['categoria'] === 'prazo' ? 'selected' : '' ?>>Prazo</option>
            <option value="custo" <?= $ocorrencia['categoria'] === 'custo' ? 'selected' : '' ?>>Custo</option>
            <option value="clima" <?= $ocorrencia['categoria'] === 'clima' ? 'selected' : '' ?>>Clima</option>
            <option value="outro" <?= $ocorrencia['categoria'] === 'outro' ? 'selected' : '' ?>>Outro</option>
        </select>

        <label>Status</label>
        <select name="status">
            <option value="aberta" <?= $ocorrencia['status'] === 'aberta' ? 'selected' : '' ?>>Aberta</option>
            <option value="em_analise" <?= $ocorrencia['status'] === 'em_analise' ? 'selected' : '' ?>>Em análise</option>
            <option value="resolvida" <?= $ocorrencia['status'] === 'resolvida' ? 'selected' : '' ?>>Resolvida</option>
        </select>

        <label>Prioridade</label>
        <select name="prioridade">
            <option value="baixa" <?= $ocorrencia['prioridade'] === 'baixa' ? 'selected' : '' ?>>Baixa</option>
            <option value="media" <?= $ocorrencia['prioridade'] === 'media' ? 'selected' : '' ?>>Média</option>
            <option value="alta" <?= $ocorrencia['prioridade'] === 'alta' ? 'selected' : '' ?>>Alta</option>
            <option value="critica" <?= $ocorrencia['prioridade'] === 'critica' ? 'selected' : '' ?>>Crítica</option>
        </select>

        <button type="submit">Atualizar Ocorrência</button>
    </form>
</div>
</main>

<?php include '../configs/footer.php'; ?>
