<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = 'ocorrencias';
require_once '../configs/banco.php';
include '../configs/header.php';

$obras = $pdo->query("SELECT id, nome FROM obras ORDER BY nome ASC")->fetchAll();

$usuarios = $pdo->query("SELECT id, nome FROM usuarios ORDER BY nome ASC")->fetchAll();
?>

<main class="main">
<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">Ocorrências <span>Cadastrar</span></h1>
            <p class="page-subtitle">Cadastar Nova Ocorrência</p>
        </div>
    </div>

    <form action="salvar.php" method="POST" class="formulario">

        <label>Obra</label>
        <select name="obra_id" required>

            <option value="">Selecione</option>

            <?php foreach ($obras as $obra): ?>

                <option value="<?= $obra['id'] ?>">
                    <?= htmlspecialchars($obra['nome']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Responsável</label>
        <select name="usuario_id">

            <option value="">Selecione</option>

            <?php foreach ($usuarios as $usuario): ?>

                <option value="<?= $usuario['id'] ?>">
                    <?= htmlspecialchars($usuario['nome']) ?>
                </option>

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

        <button type="submit">
            Salvar Ocorrência
        </button>

    </form>

</div>
</main>

</body>
</html>