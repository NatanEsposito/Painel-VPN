<!DOCTYPE html>
<html lang="pt-br">

<?php
require_once __DIR__ . '/includes/head.php';
renderHead('PÃ¡gina Inicial VPN');
?>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    <div class="text-center p-4 rounded shadow bg-white" style="min-width: 320px; max-width: 400px;">
        <h1 class="mb-4 fw-bold">Menu</h1>
        <p class="mb-4 fs-5">Selecione uma das opÃ§Ãµes</p>

        <a href="views/certificados.php" 
           class="btn btn-danger btn-lg mb-3 w-100" 
           style="font-weight: 600; padding: 0.75rem;">
            <i class="fas fa-shield-alt me-2"></i> Gerenciar Certificados
        </a>

        <a href="views/adms.php" 
           class="btn btn-info btn-lg mb-3 w-100" 
           style="font-weight: 600; padding: 0.75rem;">
            <i class="fas fa-user-cog me-2"></i> Gerenciar UsuÃ¡rios
        </a>

        <a href="views/login.php" 
           class="btn btn-secondary btn-lg w-100" 
           style="font-weight: 600; padding: 0.75rem;">
            <i class="fas fa-door-open me-2"></i> Fazer Login
        </a>
    </div>

</body>
</html>
