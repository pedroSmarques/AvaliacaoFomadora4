<?php
session_start();

// se vier ?logout=1 -> força abrir login
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
  session_unset();
  session_destroy();
}

// se já estiver logado -> manda para área dele
if (isset($_SESSION['usuario'])) {
  $role = $_SESSION['usuario']['role'];

  if ($role === 'admin') {
    header("Location: admin.php");
    exit;
  } elseif ($role === 'entregador') {
    header("Location: entregador.php");
    exit;
  } else {
    header("Location: sistema.php");
    exit;
  }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Login - Delivery Universitário</title>
  <link rel="stylesheet" href="css/login.css">
</head>

<body>
  <main class="container-login">
    <section class="card-login">
      <h1>Delivery Universitário</h1>
      <p class="subtitulo">Entre com seu e-mail e senha</p>

      <?php if (isset($_GET['erro']) && $_GET['erro'] === 'credenciais'): ?>
        <div class="alerta-erro">E-mail ou senha inválidos.</div>
      <?php endif; ?>

      <?php if (isset($_GET['erro']) && $_GET['erro'] === 'bloqueado'): ?>
        <div class="alerta-erro">
          Muitas tentativas. Registre-se para continuar.
        </div>
      <?php endif; ?>

      <form id="formLogin" method="post" action="php/valida_login.php" novalidate>
        <div class="grupo-input">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="seuemail@exemplo.com">
          <small class="erro-campo" id="erroEmail"></small>
        </div>

        <div class="grupo-input">
          <label for="senha">Senha</label>
          <input type="password" id="senha" name="senha" placeholder="Sua senha">
          <small class="erro-campo" id="erroSenha"></small>
        </div>

        <button type="submit" class="btn-vermelho">Entrar</button>

        <p class="link-cadastro">
          Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a>
        </p>
      </form>
    </section>
  </main>

  <script src="js/login.js"></script>
</body>

</html>