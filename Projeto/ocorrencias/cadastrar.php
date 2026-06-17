<?php
$current_page = 'ocorrencias';

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
            <h1 class="page-title">Ocorrências <span>Cadastrar</span></h1>
            <p class="page-subtitle">Cadastrar nova ocorrência</p>
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

        <label>Responsável pelo registro</label>
        <select name="usuario_id">
            <option value="">Sistema / não definido</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= e($usuario['id']) ?>" <?= usuarioId() === (int) $usuario['id'] ? 'selected' : '' ?>><?= e($usuario['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Título</label>
        <input type="text" name="titulo" required>

        <label>Descrição</label>
        <textarea name="descricao" required></textarea>

        <label>Categoria</label>
        <select name="categoria">
            <option value="segurança">Segurança</option>
            <option value="qualidade">Qualidade</option>
            <option value="prazo">Prazo</option>
            <option value="custo">Custo</option>
            <option value="clima">Clima</option>
            <option value="outro">Outro</option>
        </select>

        <label>Status</label>
        <select name="status">
            <option value="aberta">Aberta</option>
            <option value="em_analise">Em análise</option>
            <option value="resolvida">Resolvida</option>
        </select>

        <label>Prioridade</label>
        <select name="prioridade">
            <option value="baixa">Baixa</option>
            <option value="media">Média</option>
            <option value="alta">Alta</option>
            <option value="critica">Crítica</option>
        </select>

        <button type="submit">Salvar Ocorrência</button>
    </form>
</div>
</main>

<?php include '../configs/footer.php'; ?>
