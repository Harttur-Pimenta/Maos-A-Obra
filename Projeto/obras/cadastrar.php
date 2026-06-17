<?php
$current_page = 'obras';

require_once '../configs/banco.php';
require_once '../configs/auth.php';
exigirLogin();

if (ehAdmin()) {
    $stmt = $pdo->query("SELECT id, nome FROM usuarios WHERE perfil IN ('admin','engenheiro','tecnico') ORDER BY nome ASC");
    $usuarios = $stmt->fetchAll();
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
            <h1 class="page-title">Obras <span>Cadastrar</span></h1>
            <p class="page-subtitle">Inserir dados do novo projeto</p>
        </div>
    </div>

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
        <select name="responsavel_id" required>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= e($usuario['id']) ?>"><?= e($usuario['nome']) ?></option>
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
</div>
</main>

<?php include '../configs/footer.php'; ?>
