<?php
session_start();

require_once '../configs/banco.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: ../dashboard/index.php");
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $sql = "SELECT * 
            FROM usuarios 
            WHERE email = :email 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':email' => $email
    ]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {

        if (password_verify($senha, $usuario['senha_hash']) || md5($senha) === $usuario['senha_hash']) {

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];

            header("Location: ../dashboard/index.php");
            exit;
        }
    }

    $erro = "E-mail ou senha inválidos.";
}
?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Mãos à Obra</title>

    <link rel="stylesheet" href="./login.css">

</head>

<body>

    <div class="login-container">

        <div class="login-box">

            <h1>Mãos à Obra</h1>

            <p class="subtitle">
                Entre para acessar o sistema
            </p>

            <?php if (!empty($erro)): ?>

                <div class="alert-erro">
                    <?= htmlspecialchars($erro) ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="form-group">

                    <label>E-mail</label>

                    <input 
                        type="email"
                        name="email"
                        placeholder="Digite seu e-mail"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Senha</label>

                    <input 
                        type="password"
                        name="senha"
                        placeholder="Digite sua senha"
                        required
                    >

                </div>

                <button type="submit">
                    Entrar
                </button>

            </form>

        </div>

    </div>

</body>

</html>