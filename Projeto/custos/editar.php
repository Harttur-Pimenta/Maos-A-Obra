<?php
$current_page = 'custos';
require_once '../configs/banco.php';
include '../configs/header.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die('ID do custo não informado.');
}

$sql = "SELECT * FROM custos_obra WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':id' => $id
]);

$custo = $stmt->fetch();

if (!$custo) {
    die('Custo não encontrado.');
}

$obras = $pdo->query("SELECT id, nome FROM obras ORDER BY nome ASC")->fetchAll();

$usuarios = $pdo->query("SELECT id, nome FROM usuarios ORDER BY nome ASC")->fetchAll();
?>

<main class="main">
<div class="container">

    <div class="page-header">
        <div>
            <h1 class="page-title">Custos e Materiais <span>Editar</span></h1>
            <p class="page-subtitle">Editar Gasto Selecionado</p>
        </div>
    </div>

    <form action="atualizar.php" method="POST" class="formulario">

        <input type="hidden" name="id" value="<?= $custo['id'] ?>">

        <label>Obra</label>
        <select name="obra_id" required>
            <option value="">Selecione</option>

            <?php foreach ($obras as $obra): ?>
                <option 
                    value="<?= $obra['id'] ?>"
                    <?= $custo['obra_id'] == $obra['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($obra['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Descrição</label>
        <input 
            type="text" 
            name="descricao" 
            value="<?= htmlspecialchars($custo['descricao']) ?>" 
            required
        >

        <label>Tipo</label>
        <select name="tipo" required>
            <option value="material" <?= $custo['tipo'] == 'material' ? 'selected' : '' ?>>Material</option>
            <option value="servico" <?= $custo['tipo'] == 'servico' ? 'selected' : '' ?>>Serviço</option>
            <option value="equipamento" <?= $custo['tipo'] == 'equipamento' ? 'selected' : '' ?>>Equipamento</option>
            <option value="outro" <?= $custo['tipo'] == 'outro' ? 'selected' : '' ?>>Outro</option>
        </select>

        <label>Responsável</label>
        <select name="usuario_id">
            <option value="">Selecione</option>

            <?php foreach ($usuarios as $usuario): ?>
                <option 
                    value="<?= $usuario['id'] ?>"
                    <?= $custo['usuario_id'] == $usuario['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($usuario['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Quantidade Planejada</label>
        <input 
            type="number" 
            step="0.001" 
            name="qtd_planejada" 
            value="<?= $custo['qtd_planejada'] ?>"
        >

        <label>Quantidade Realizada</label>
        <input 
            type="number" 
            step="0.001" 
            name="qtd_realizada" 
            value="<?= $custo['qtd_realizada'] ?>"
        >

        <label>Valor Planejado</label>
        <input 
            type="number" 
            step="0.01" 
            name="valor_planejado" 
            value="<?= $custo['valor_planejado'] ?>"
        >

        <label>Valor Realizado</label>
        <input 
            type="number" 
            step="0.01" 
            name="valor_realizado" 
            value="<?= $custo['valor_realizado'] ?>"
        >

        <label>Data do Lançamento</label>
        <input 
            type="date" 
            name="data_lancamento" 
            value="<?= $custo['data_lancamento'] ?>"
        >

        <button type="submit">Salvar</button>

    </form>

</div>
</main>

</body>
</html>