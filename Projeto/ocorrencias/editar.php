<?php
$current_page = 'ocorrencias';
require_once '../configs/banco.php';
include '../configs/header.php';

$id = $_GET['id'] ?? null;

$sql = "SELECT * FROM ocorrencias WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':id' => $id
]);

$ocorrencia = $stmt->fetch();

$obras = $pdo->query("SELECT id, nome FROM obras ORDER BY nome ASC")->fetchAll();

$usuarios = $pdo->query("SELECT id, nome FROM usuarios ORDER BY nome ASC")->fetchAll();
?>

<main class="main">
<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">Ocorrências <span>Editar</span></h1>
            <p class="page-subtitle">Editar Ocorrência Selecionada</p>
        </div>
    </div>

    <form action="atualizar.php" method="POST" class="formulario">

        <input type="hidden" name="id" value="<?= $ocorrencia['id'] ?>">

        <label>Obra</label>
        <select name="obra_id" required>

            <?php foreach ($obras as $obra): ?>

                <option 
                    value="<?= $obra['id'] ?>"
                    <?= $ocorrencia['obra_id'] == $obra['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($obra['nome']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Responsável</label>
        <select name="usuario_id">

            <option value="">Selecione</option>

            <?php foreach ($usuarios as $usuario): ?>

                <option 
                    value="<?= $usuario['id'] ?>"
                    <?= $ocorrencia['usuario_id'] == $usuario['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($usuario['nome']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Título</label>
        <input 
            type="text" 
            name="titulo" 
            value="<?= htmlspecialchars($ocorrencia['titulo']) ?>" 
            required
        >

        <label>Descrição</label>
        <textarea name="descricao"><?= htmlspecialchars($ocorrencia['descricao']) ?></textarea>

        <label>Categoria</label>
        <select name="categoria">

            <option value="segurança" <?= $ocorrencia['categoria'] == 'segurança' ? 'selected' : '' ?>>Segurança</option>

            <option value="qualidade" <?= $ocorrencia['categoria'] == 'qualidade' ? 'selected' : '' ?>>Qualidade</option>

            <option value="prazo" <?= $ocorrencia['categoria'] == 'prazo' ? 'selected' : '' ?>>Prazo</option>

            <option value="custo" <?= $ocorrencia['categoria'] == 'custo' ? 'selected' : '' ?>>Custo</option>

            <option value="clima" <?= $ocorrencia['categoria'] == 'clima' ? 'selected' : '' ?>>Clima</option>

            <option value="outro" <?= $ocorrencia['categoria'] == 'outro' ? 'selected' : '' ?>>Outro</option>

        </select>

        <label>Status</label>
        <select name="status">

            <option value="aberta" <?= $ocorrencia['status'] == 'aberta' ? 'selected' : '' ?>>Aberta</option>

            <option value="em_analise" <?= $ocorrencia['status'] == 'em_analise' ? 'selected' : '' ?>>Em análise</option>

            <option value="resolvida" <?= $ocorrencia['status'] == 'resolvida' ? 'selected' : '' ?>>Resolvida</option>

        </select>

        <label>Prioridade</label>
        <select name="prioridade">

            <option value="baixa" <?= $ocorrencia['prioridade'] == 'baixa' ? 'selected' : '' ?>>Baixa</option>

            <option value="media" <?= $ocorrencia['prioridade'] == 'media' ? 'selected' : '' ?>>Média</option>

            <option value="alta" <?= $ocorrencia['prioridade'] == 'alta' ? 'selected' : '' ?>>Alta</option>

            <option value="critica" <?= $ocorrencia['prioridade'] == 'critica' ? 'selected' : '' ?>>Crítica</option>

        </select>

        <button type="submit">
            Atualizar Ocorrência
        </button>

    </form>

</div>
</main>

</body>
</html>