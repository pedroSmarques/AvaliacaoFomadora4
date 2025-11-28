<?php
session_start();

// Proteção
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'entregador') {
    header("Location: login.php");
    exit;
}

$nome = $_SESSION['usuario']['nome'];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Entregador - Delivery Universitário</title>
    <link rel="stylesheet" href="css/entregador.css">
</head>

<body>

    <header class="topo">
        <h1>Bem-vindo, <?php echo htmlspecialchars($nome); ?></h1>
        <a href="php/logout.php" class="btn-sair">Sair</a>
    </header>

    <main class="container">

        <section class="painel">
            <h2>Pedidos Disponíveis</h2>
            <div id="listaPedidos" class="lista-pedidos">
                <p class="carregando">Carregando pedidos...</p>
            </div>
        </section>

    </main>

    <!-- MODAL PARA TOKEN -->
    <div id="modalToken" class="modal hidden">
        <div class="modal-conteudo">
            <h2>Finalizar entrega</h2>
            <p>Digite o token informado pelo cliente:</p>

            <input type="text" id="inputToken" maxlength="6" class="campo-token">

            <small id="erroModal" class="erro-modal"></small>

            <div class="botoes-modal">
                <button id="btnCancelarModal" class="btn-modal btn-cancelar">Cancelar</button>
                <button id="btnConfirmarModal" class="btn-modal btn-confirmar">Confirmar</button>
            </div>
        </div>
    </div>

    <script src="js/entregador.js"></script>
</body>

</html>
