<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['usuario']['role'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

$id   = $_SESSION['usuario']['id'];
$nome = $_SESSION['usuario']['nome'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Sistema - Cliente</title>
    <link rel="stylesheet" href="css/sistema.css">
</head>

<body>

    <header class="topo">
        <h1>Olá, <?php echo htmlspecialchars($nome); ?> 👋</h1>
        <a class="btn-sair" href="php/logout.php">Sair</a>
    </header>

    <!-- 🔥 Disponibiliza o ID do usuário para o JavaScript -->
    <script>
        const usuarioID = <?php echo json_encode($id); ?>;
    </script>

    <main class="container">

        <!-- LISTA DE PRODUTOS -->
        <section class="produtos">
            <h2>Escolha sua refeição</h2>
            <div id="listaProdutos" class="lista-produtos"></div>
        </section>

        <!-- CARRINHO -->
        <section class="carrinho">
            <h2>Seu pedido</h2>
            <div id="itensCarrinho"></div>
            <div id="totalCarrinho" class="total"></div>
            <button id="btnFinalizar" class="btn-vermelho">Finalizar Pedido</button>
        </section>

        <!-- FORMULÁRIO DE ENDEREÇO -->
        <section class="endereco">
            <h2>Endereço de Entrega</h2>

            <form id="formEndereco">

                <label>CEP</label>
                <input type="text" id="cep" maxlength="9">
                <small class="erro" id="erroCep"></small>

                <label>Rua</label>
                <input type="text" id="rua" readonly>

                <label>Número</label>
                <input type="text" id="numero">

                <label>Complemento</label>
                <input type="text" id="complemento">

                <button id="btnSalvarEndereco" type="button" class="btn-preto">Salvar Endereço</button>

            </form>

            <p class="atencao">Você deve salvar o endereço antes de finalizar o pedido.</p>

        </section>

    </main>

    <!-- LOADING -->
    <div id="loading" class="loading hidden">
        <div class="spinner"></div>
        <p>Enviando pedido...</p>
    </div>

    <script src="js/sistema.js"></script>
</body>

</html>