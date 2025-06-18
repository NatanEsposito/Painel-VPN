<?php
session_start();
require_once __DIR__ . '/../includes/funcoes.php';

$usuariosFile = __DIR__ . '/../storage/usuarios.json';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

    // ValidaÃ§Ã£o: Campo de email vazio ou invÃ¡lido
    if (strlen($email) < 3 || strlen($email) > 30 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe o username para realizar o processo de entrada;';
    }
    // ValidaÃ§Ã£o: Senha em branco
    elseif (empty($senha)) {
        $erro = 'Informe o password do usuÃ¡rio para realizar o processo de entrada;';
    }
    else {
        // Carregar usuÃ¡rios do JSON
        if (file_exists($usuariosFile)) {
            $usuarios = json_decode(file_get_contents($usuariosFile), true);
        } else {
            $usuarios = [];
        }

        $usuario = buscarUsuarioPorEmail($email, $usuarios);

        if ($usuario && $usuario['ativo'] === true && password_verify($senha, $usuario['senha'])) {
            // Login OK
            $_SESSION['usuario'] = [
                'email' => $usuario['email'],
                'nome' => $usuario['nome']
            ];
            header('Location: /index.php');
            exit;
        } else {
            $erro = 'UsuÃ¡rio ou senha estÃ£o incorretos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Login - VPN Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* Centraliza o container vertical e horizontalmente */
        body, html {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-box {
            background: white;
            padding: 2.5rem 3rem;
            border-radius: 0.75rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-title {
            margin-bottom: 1.5rem;
            font-weight: 700;
            font-size: 1.8rem;
            color: #212529;
            text-align: center;
        }
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            font-size: 1.15rem;
            font-weight: 600;
            background-color: #dc3545;
            border: none;
            box-shadow: 0 4px 12px rgba(220,53,69,0.5);
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #b02a37;
        }
        .alert {
            font-size: 0.9rem;
            margin-bottom: 1.2rem;
        }
        label.form-label {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 class="login-title">Login</h2>
        <?php if ($erro): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email (Username):</label>
                <input type="email" name="email" id="email" class="form-control" maxlength="30" required />
            </div>
            <div class="mb-4">
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" name="senha" id="senha" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-login">Entrar</button>
        </form>
    </div>
</body>
</html>
