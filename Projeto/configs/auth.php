<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioLogado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function exigirLogin(): void
{
    if (!usuarioLogado()) {
        header('Location: ../login/login.php');
        exit;
    }
}

function perfilUsuario(): string
{
    return $_SESSION['usuario_perfil'] ?? '';
}

function usuarioId(): ?int
{
    return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
}

function usuarioNome(): string
{
    return $_SESSION['usuario_nome'] ?? 'Usuário';
}

function ehAdmin(): bool
{
    return perfilUsuario() === 'admin';
}

function ehEngenheiro(): bool
{
    return perfilUsuario() === 'engenheiro';
}

function e($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function obraPertenceAoUsuario(PDO $pdo, int $obraId): bool
{
    if (ehAdmin()) {
        return true;
    }

    if (!ehEngenheiro()) {
        return false;
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM obras WHERE id = :id AND responsavel_id = :usuario_id');
    $stmt->execute([
        ':id' => $obraId,
        ':usuario_id' => usuarioId()
    ]);

    return (int) $stmt->fetchColumn() > 0;
}

function custoPertenceAoUsuario(PDO $pdo, int $custoId): bool
{
    if (ehAdmin()) {
        return true;
    }

    if (!ehEngenheiro()) {
        return false;
    }

    $stmt = $pdo->prepare('SELECT COUNT(*)
                           FROM custos_obra
                           INNER JOIN obras ON custos_obra.obra_id = obras.id
                           WHERE custos_obra.id = :id
                             AND obras.responsavel_id = :usuario_id');
    $stmt->execute([
        ':id' => $custoId,
        ':usuario_id' => usuarioId()
    ]);

    return (int) $stmt->fetchColumn() > 0;
}

function ocorrenciaPertenceAoUsuario(PDO $pdo, int $ocorrenciaId): bool
{
    if (ehAdmin()) {
        return true;
    }

    if (!ehEngenheiro()) {
        return false;
    }

    $stmt = $pdo->prepare('SELECT COUNT(*)
                           FROM ocorrencias
                           INNER JOIN obras ON ocorrencias.obra_id = obras.id
                           WHERE ocorrencias.id = :id
                             AND obras.responsavel_id = :usuario_id');
    $stmt->execute([
        ':id' => $ocorrenciaId,
        ':usuario_id' => usuarioId()
    ]);

    return (int) $stmt->fetchColumn() > 0;
}

function negarAcesso(): void
{
    http_response_code(403);
    die('Acesso negado.');
}
