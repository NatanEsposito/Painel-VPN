<?php
require_once __DIR__ . '/../includes/auth.php';

$tituloPagina = "Lista de Administradores";
include_once __DIR__ . '/../includes/head.php';

$caminhoUsuarios = __DIR__ . '/../storage/usuarios.json';
$usuarios = [];

if (file_exists($caminhoUsuarios)) {
    $usuarios = json_decode(file_get_contents($caminhoUsuarios), true);
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao']) && isset($_POST['selecionados'])) {
        $idsSelecionados = $_POST['selecionados'];

        if ($_POST['acao'] === 'desativar') {
            foreach ($usuarios as &$usuario) {
                if (in_array($usuario['email'], $idsSelecionados)) {
                    $usuario['ativo'] = false;
                }
            }
            unset($usuario);
            $mensagem = '<div class="alert alert-warning">UsuÃ¡rios desativados.</div>';
        }

        if ($_POST['acao'] === 'ativar') {
            foreach ($usuarios as &$usuario) {
                if (in_array($usuario['email'], $idsSelecionados)) {
                    $usuario['ativo'] = true;
                }
            }
            unset($usuario);
            $mensagem = '<div class="alert alert-success">UsuÃ¡rios ativados.</div>';
        }

        if ($_POST['acao'] === 'remover') {
            $usuarios = array_filter($usuarios, function ($usuario) use ($idsSelecionados) {
                return !in_array($usuario['email'], $idsSelecionados);
            });
            $usuarios = array_values($usuarios);
            $mensagem = '<div class="alert alert-danger">UsuÃ¡rios removidos.</div>';
        }

        file_put_contents($caminhoUsuarios, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<?php renderHead($tituloPagina); ?>
<body class="bg-light d-flex flex-column min-vh-100">
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container my-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Administradores</h4>
            <a href="/views/cadastro.php" class="btn btn-light text-primary fw-bold">+ Inserir</a>
        </div>

        <div class="card-body bg-white text-dark">

            <?= $mensagem ?>

            <form method="POST">
                <div class="d-flex justify-content-start mb-3 gap-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Editar
                        </button>
                        <ul class="dropdown-menu">
                            <li><button type="submit" name="acao" value="desativar" class="dropdown-item text-warning">Desativar acesso</button></li>
                            <li><button type="submit" name="acao" value="ativar" class="dropdown-item text-success">Ativar acesso</button></li>
                            <li><button type="submit" name="acao" value="remover" class="dropdown-item text-danger">Remover usuÃ¡rio</button></li>
                        </ul>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Selecionar</th>
                                <th>Email</th>
                                <th>Nome</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="4" class="text-muted">Nenhum administrador cadastrado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selecionados[]" value="<?= htmlspecialchars($usuario['email']); ?>">
                                        </td>
                                        <td><?= htmlspecialchars($usuario['email']); ?></td>
                                        <td><?= htmlspecialchars($usuario['nome']); ?></td>
					<td>
  					<span class="badge <?= $usuario['ativo'] ? 'bg-success' : 'bg-warning text-dark'; ?>">
        				<?= $usuario['ativo'] ? 'Ativo' : 'Inativo'; ?>
    					</span>
					
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>
