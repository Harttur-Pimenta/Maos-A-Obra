<?php
require_once __DIR__ . '/auth.php';
exigirLogin();

$nav_items = [
    'dashboard'   => ['href' => '../dashboard/index.php',    'label' => 'Dashboard'],
    'obras'       => ['href' => '../obras/index.php',        'label' => 'Obras'],
    'custos'      => ['href' => '../custos/index.php',       'label' => 'Materiais e Custos'],
    'ocorrencias' => ['href' => '../ocorrencias/index.php',  'label' => 'Ocorrências'],
    'historico'   => ['href' => '../historico/index.php',    'label' => 'Histórico'],
    'relatorios'  => ['href' => '../relatorios/index.php',   'label' => 'Relatórios']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../img/favicon.png?v=99">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../configs/style.css">
    <title>Mãos à Obra — <?= e(ucfirst($current_page ?? 'Sistema')) ?></title>
</head>
<body>
<div class="page-wrapper">
<header class="header">
    <div class="container">
        <a href="../dashboard/index.php" class="logo">
            <div class="logo-mark">MO</div>
            <div class="logo-text">Mãos à <span>Obra</span></div>
        </a>
        <nav class="navbar">
            <?php foreach ($nav_items as $key => $item): ?>
                <a href="<?= e($item['href']) ?>" class="<?= ($current_page ?? '') === $key ? 'active' : '' ?>">
                    <?= e($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="header-actions">
            <div class="user-summary">
                <strong><?= e(usuarioNome()) ?></strong>
                <small><?= e(perfilUsuario()) ?></small>
            </div>
            <a href="../login/logout.php" class="btn-logout">Sair</a>
            <div class="avatar" title="Meu perfil">
                <?= e(strtoupper(substr(usuarioNome(), 0, 1))) ?>
            </div>
        </div>
    </div>
</header>
