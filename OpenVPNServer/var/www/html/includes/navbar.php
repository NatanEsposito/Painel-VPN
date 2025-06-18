<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
  <!-- Toggler/collapse button -->
  <button class="navbar-toggler mx-2 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- All items centered -->
  <div class="collapse navbar-collapse justify-content-center text-center" id="navbarContent">
    <ul class="navbar-nav align-items-center">

      <!-- Home -->
      <li class="nav-item px-3">
        <a class="nav-link text-dark px-3 py-2 rounded hover-bg-light" href="/index.php">
          <i class="fa-solid fa-house-chimney me-1"></i> Home
        </a>
      </li>

      <!-- Certificados -->
      <li class="nav-item px-3">
        <a class="nav-link text-dark px-3 py-2 rounded hover-bg-light" href="/views/certificados.php">
          <i class="fa-solid fa-certificate me-1"></i> Certificados
        </a>
      </li>

      <!-- ADM Dropdown -->
      <li class="nav-item dropdown px-3">
        <a
          class="nav-link dropdown-toggle text-dark px-3 py-2 rounded hover-bg-light"
          href="#"
          id="dropdown2"
          role="button"
          data-bs-toggle="dropdown"
          aria-expanded="false">
          <i class="fa-solid fa-user-shield me-1"></i> ADM
        </a>
        <ul class="dropdown-menu rounded-3 shadow border border-secondary-subtle bg-light" aria-labelledby="dropdown2">
          <li><a class="dropdown-item text-dark" href="/views/adms.php">InÃ­cio</a></li>
          <li><a class="dropdown-item text-dark" href="/views/cadastro.php">Cadastrar</a></li>
        </ul>
      </li>

      <!-- Sair -->
      <li class="nav-item px-3">
        <a class="nav-link text-dark px-3 py-2 rounded hover-bg-danger" href="/views/logout.php">
          <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
        </a>
      </li>
    </ul>
  </div>
</nav>

<!-- Hover effect helpers -->
<style>
  .hover-bg-light:hover { background-color: rgba(0, 0, 0, 0.05) !important; }
  .hover-bg-danger:hover { background-color: rgba(220, 53, 69, 0.2) !important; }
</style>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
