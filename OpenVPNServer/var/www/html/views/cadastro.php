<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funcoes.php';

$usuariosFile = __DIR__ . '/../storage/usuarios.json';
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    $confirmarSenha = isset($_POST['confirmarSenha']) ? $_POST['confirmarSenha'] : '';

    if (strlen($email) < 3 || strlen($email) > 30 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um email vÃ¡lido com atÃ© 30 caracteres.';
    } elseif (empty($nome)) {
        $erro = 'Informe o nome completo.';
    } elseif (empty($senha) || empty($confirmarSenha)) {
        $erro = 'Informe a senha e a confirmaÃ§Ã£o de senha.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'A senha e a confirmaÃ§Ã£o de senha nÃ£o coincidem.';
    } elseif (
        strlen($senha) < 8 ||
        !preg_match('/[A-Za-z]/', $senha) ||
        !preg_match('/\d/', $senha) ||
        !preg_match('/[!@#$%&*\-_\+=]/', $senha)
    ) {
        $erro = 'A senha deve ter no mÃ­nimo 8 caracteres, incluindo uma letra, um nÃºmero e um caractere especial (!@#$%&*-_+=).';
    } else {
        if (file_exists($usuariosFile)) {
            $usuarios = json_decode(file_get_contents($usuariosFile), true);
        } else {
            $usuarios = [];
        }

        if (buscarUsuarioPorEmail($email, $usuarios)) {
            $erro = 'JÃ¡ existe um usuÃ¡rio com esse email.';
        } else {
            $novoUsuario = [
                'email' => $email,
                'nome' => $nome,
                'senha' => password_hash($senha, PASSWORD_DEFAULT),
                'ativo' => true
            ];
            $usuarios[] = $novoUsuario;
            file_put_contents($usuariosFile, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $sucesso = 'UsuÃ¡rio cadastrado com sucesso.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<?php
require_once __DIR__ . '/../includes/head.php';
renderHead('Cadastro de UsuÃ¡rio');
?>

<body class="bg-light d-flex flex-column min-vh-100">

<!-- Navbar -->
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container d-flex justify-content-center align-items-center flex-grow-1">
    <div class="card shadow-lg border-0 mt-5 mb-5 w-100" style="max-width: 500px;">
        <div class="card-header bg-primary text-white text-center rounded-top">
            <h4 class="mb-0">Cadastro de UsuÃ¡rio</h4>
        </div>
        <div class="card-body p-4 text-dark">
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
            <?php elseif ($sucesso): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($sucesso); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email (Username):</label>
                    <input type="email" name="email" id="email" class="form-control" maxlength="30" required>
                </div>

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome completo:</label>
                    <input type="text" name="nome" id="nome" minlength="3" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha:</label>
                    <input type="password" name="senha" id="senha" minlength="8" class="form-control" required>
                    <div class="form-text">
                        A senha deve ter no mÃ­nimo 8 caracteres, incluindo uma letra, um nÃºmero e um caractere especial (!@#$%&*-_+=).
                    </div>
                </div>

                <div class="mb-3">
                    <label for="confirmarSenha" class="form-label">Confirme a senha:</label>
                    <input type="password" name="confirmarSenha" id="confirmarSenha" minlength="8" class="form-control" required>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
