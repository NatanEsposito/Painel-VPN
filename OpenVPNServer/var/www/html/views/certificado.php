<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funcoes.php';

$jsonPath = __DIR__ . '/../storage/registros.json';

$msgSucesso = '';
$msgRemovido = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['executar'])) {
        $id = gerarIdUnico();
        $safeId = escapeshellarg($id);
        $comando = "sudo /usr/bin/python3 /opt/vpn-cert-generator/gerar_certificado.py $safeId";
        shell_exec($comando);

        $origem = "/var/www/html/storage/{$id}_cert.zip";
        $destino = __DIR__ . "/../storage/{$id}_cert.zip";
        if (file_exists($origem) && !file_exists($destino)) {
            rename($origem, $destino);
        }

        $registro = [
            "id" => $id,
            "data" => date("Y-m-d H:i:s"),
            "validade" => date("Y-m-d", strtotime("+7 days")),
        ];
        $dadosAtuais = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
        $dadosAtuais[] = $registro;
        file_put_contents($jsonPath, json_encode($dadosAtuais, JSON_PRETTY_PRINT));

        $msgSucesso = "Certificado gerado com ID: <strong>" . htmlspecialchars($id) . "</strong>";
    }

    if (isset($_POST['apagar'], $_POST['remover']) && is_array($_POST['remover'])) {
        $dadosAtuais = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
        foreach ($_POST['remover'] as $id) {
            $safeId = escapeshellarg($id);
            $comando = "sudo /usr/bin/python3 /opt/vpn-cert-generator/deletar_certificado.py $safeId";
            shell_exec($comando);

            $dadosAtuais = array_filter($dadosAtuais, fn($item) => $item['id'] !== $id);
        }
        file_put_contents($jsonPath, json_encode(array_values($dadosAtuais), JSON_PRETTY_PRINT));
        $msgRemovido = "Certificados selecionados foram removidos.";
    }
}

$lista = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
?>

<!DOCTYPE html>
<html lang="pt-br">

<?php
require_once __DIR__ . '/../includes/head.php';
renderHead('Gerar Certificado VPN');
?>

<body class="bg-dark text-light d-flex flex-column min-vh-100">

<!-- NAVBAR -->
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<main class="container py-5 flex-grow-1">
    <h1 class="mb-4 fw-bold text-white">Gerar novo certificado VPN</h1>

    <?php if (!empty($msgSucesso)) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $msgSucesso ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($msgRemovido)) : ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($msgRemovido) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <form method="POST" class="mb-5">
        <button type="submit" name="executar" value="1" class="btn btn-lg btn-outline-light">
            <i class="bi bi-plus-circle me-2"></i> Gerar certificado
        </button>
    </form>

    <h2 class="mb-3 text-white">Certificados existentes</h2>

    <?php if (count($lista) > 0) : ?>
        <form method="POST" onsubmit="return confirm('VocÃª realmente deseja excluir os certificados selecionados?');">
            <div class="table-responsive shadow rounded">
                <table class="table table-dark table-striped table-hover align-middle mb-3 text-light">
                    <thead>
                        <tr class="text-warning">
                            <th scope="col" class="text-center" style="width:5%;">Remover</th>
                            <th scope="col" class="text-center" style="width:10%;">Download</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Data de criaÃ§Ã£o</th>
                            <th scope="col">Data de expiraÃ§Ã£o</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista as $cert) : ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="remover[]" value="<?= htmlspecialchars($cert['id']) ?>" aria-label="Selecionar certificado <?= htmlspecialchars($cert['id']) ?>">
                                </td>
                                <td class="text-center">
                                    <a href="baixar.php?id=<?= urlencode($cert['id']) ?>" class="btn btn-sm btn-success" title="Baixar certificado <?= htmlspecialchars($cert['id']) ?>">
                                        <i class="bi bi-download"></i> Baixar
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($cert['id']) ?></td>
                                <td><?= htmlspecialchars($cert['data']) ?></td>
                                <td><?= htmlspecialchars($cert['validade']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <button type="submit" name="apagar" value="1" class="btn btn-danger">
                <i class="bi bi-trash"></i> Remover selecionados
            </button>
        </form>
    <?php else : ?>
        <p class="text-secondary fst-italic">Nenhum certificado gerado ainda.</p>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    body {
        background-color: #121212;
        color: #ddd;
    }
    .table-dark {
        background-color: #1e1e1e;
    }
    .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #2a2a2a;
    }
    .table-hover > tbody > tr:hover {
        background-color: #444444;
    }
    thead tr {
        border-bottom: 2px solid #ffb300;
    }
    .btn-outline-light:hover {
        background-color: #444;
        color: #fff;
    }
    .btn-close-white {
        filter: invert(1);
    }
</style>

</body>
</html>
