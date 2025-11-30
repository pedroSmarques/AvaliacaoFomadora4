<?php

session_start();

// Verifica se está logado e se é admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$nomeAdmin = $_SESSION['usuario']['nome'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>

    <div class="layout">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="logo">
                <h2>Delivery Universitário</h2>
                <span class="papel">Administrador</span>
            </div>

            <nav class="menu">
                <button class="item-menu ativo" data-alvo="sec-dashboard">Dashboard</button>
                <button class="item-menu" data-alvo="sec-produtos">Produtos</button>
                <button class="item-menu" data-alvo="sec-usuarios">Clientes</button>
                <button class="item-menu" data-alvo="sec-entregadores">Entregadores</button>
                <button class="item-menu" data-alvo="sec-pedidos">Pedidos</button>
                <button class="item-menu" data-alvo="sec-logs">Logs de acesso</button>
            </nav>

            <div class="rodape-sidebar">
                <p>Logado como</p>
                <strong><?php echo htmlspecialchars($nomeAdmin); ?></strong>
                <a href="php/logout.php" class="btn-sair">Sair</a>
            </div>
        </aside>

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="conteudo">

            <!-- DASHBOARD RESUMO -->
            <section id="sec-dashboard" class="secao visivel">
                <h1>Visão Geral</h1>
                <p class="subtitulo">Resumo rápido do sistema.</p>

                <div class="cards-resumo">
                    <div class="card-info" id="card-total-produtos">
                        <h2>Produtos</h2>
                        <p id="numProdutos">0</p>
                    </div>

                    <div class="card-info" id="card-total-usuarios">
                        <h2>Clientes</h2>
                        <p id="numUsuarios">0</p>
                    </div>

                    <div class="card-info" id="card-total-entregadores">
                        <h2>Entregadores</h2>
                        <p id="numEntregadores">0</p>
                    </div>

                    <div class="card-info" id="card-total-pedidos">
                        <h2>Pedidos</h2>
                        <p id="numPedidos">0</p>
                    </div>
                </div>
            </section>

            <!-- PRODUTOS (CRUD) -->
            <section id="sec-produtos" class="secao">
                <h1>Produtos</h1>
                <p class="subtitulo">Cadastre, edite e remova produtos do cardápio.</p>

                <!-- FORMULÁRIO PRODUTO -->
                <div class="card-form">
                    <h2 id="tituloFormProduto">Adicionar Produto</h2>
                    <form id="formProduto">
                        <input type="hidden" id="produto_id" name="produto_id">

                        <div class="grupo-input">
                            <label for="nome_produto">Nome do Produto</label>
                            <input type="text" id="nome_produto" name="nome_produto">
                            <small class="erro-campo" id="erroNomeProduto"></small>
                        </div>

                        <div class="grupo-input">
                            <label for="preco_produto">Preço (R$)</label>
                            <input type="text" id="preco_produto" name="preco_produto" placeholder="19,90">
                            <small class="erro-campo" id="erroPrecoProduto"></small>
                        </div>

                        <div class="botoes-form">
                            <button type="submit" class="btn-vermelho" id="btnSalvarProduto">Salvar</button>
                            <button type="button" class="btn-preto" id="btnCancelarEdicao" style="display:none;">Cancelar</button>
                        </div>
                    </form>
                </div>

                <!-- LISTAGEM PRODUTOS -->
                <div class="card-tabela">
                    <h2>Lista de Produtos</h2>
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Preço (R$)</th>
                                <th>Ativo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProdutos">
                            <!-- preenchido via JS -->
                        </tbody>
                    </table>
                    <div id="msgProdutos" class="mensagem"></div>
                </div>
            </section>

            <!-- CLIENTES -->
            <section id="sec-usuarios" class="secao">
                <h1>Clientes</h1>
                <p class="subtitulo">Listagem e controle de clientes cadastrados.</p>

                <div class="card-tabela">
                    <h2>Lista de Clientes</h2>

                    <!-- FILTROS CLIENTE -->
                    <div class="filtros">
                        <div class="grupo-input">
                            <label>CPF</label>
                            <input type="text" id="filtroClienteCpf" placeholder="Apenas números">
                        </div>
                        <div class="grupo-input">
                            <label>Nome</label>
                            <input type="text" id="filtroClienteNome" placeholder="Nome do cliente">
                        </div>
                        <div class="grupo-input">
                            <label>E-mail</label>
                            <input type="text" id="filtroClienteEmail" placeholder="email@exemplo.com">
                        </div>
                        <div class="botoes-form">
                            <button type="button" class="btn-preto" id="btnFiltrarClientes">Filtrar</button>
                            <button type="button" class="btn-preto" id="btnLimparFiltroClientes">Limpar</button>
                        </div>
                    </div>

                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>CPF</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyClientes">
                            <!-- via JS -->
                        </tbody>
                    </table>
                    <div id="msgClientes" class="mensagem"></div>
                </div>

                <!-- FORM EDITAR CLIENTE -->
                <div class="card-form" id="cardEditarCliente" style="display:none;">
                    <h2>Editar Cliente</h2>
                    <form id="formEditarCliente">
                        <input type="hidden" id="id_cliente_editar">

                        <div class="grupo-input">
                            <label>Nome</label>
                            <input type="text" id="nome_cliente_editar">
                        </div>

                        <div class="grupo-input">
                            <label>E-mail</label>
                            <input type="email" id="email_cliente_editar">
                        </div>

                        <div class="grupo-input">
                            <label>CPF</label>
                            <input type="text" id="cpf_cliente_editar">
                        </div>

                        <div class="botoes-form">
                            <button type="submit" class="btn-vermelho" id="btnSalvarClienteEdicao">Salvar alterações</button>
                            <button type="button" class="btn-preto" id="btnCancelarClienteEdicao">Cancelar</button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- ENTREGADORES -->
            <section id="sec-entregadores" class="secao">
                <h1>Entregadores</h1>
                <p class="subtitulo">Controle dos entregadores cadastrados.</p>

                <div class="card-tabela">
                    <h2>Lista de Entregadores</h2>

                    <!-- FILTROS ENTREGADOR -->
                    <div class="filtros">
                        <div class="grupo-input">
                            <label>Nome</label>
                            <input type="text" id="filtroEntNome" placeholder="Nome do entregador">
                        </div>
                        <div class="grupo-input">
                            <label>E-mail</label>
                            <input type="text" id="filtroEntEmail" placeholder="email@exemplo.com">
                        </div>
                        <div class="botoes-form">
                            <button type="button" class="btn-preto" id="btnFiltrarEntregadores">Filtrar</button>
                            <button type="button" class="btn-preto" id="btnLimparFiltroEntregadores">Limpar</button>
                        </div>
                    </div>

                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Veículo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyEntregadores">
                            <!-- via JS -->
                        </tbody>
                    </table>
                    <div id="msgEntregadores" class="mensagem"></div>
                </div>

                <!-- FORM EDITAR ENTREGADOR -->
                <div class="card-form" id="cardEditarEntregador" style="display:none;">
                    <h2>Editar Entregador</h2>
                    <form id="formEditarEntregador">
                        <input type="hidden" id="id_entregador_editar">

                        <div class="grupo-input">
                            <label>Nome</label>
                            <input type="text" id="nome_entregador_editar">
                        </div>

                        <div class="grupo-input">
                            <label>E-mail</label>
                            <input type="email" id="email_entregador_editar">
                        </div>

                        <div class="grupo-input">
                            <label>Veículo</label>
                            <select id="veiculo_entregador_editar">
                                <option value="">Selecione...</option>
                                <option value="moto">Moto</option>
                                <option value="carro">Carro</option>
                                <option value="bicicleta">Bicicleta</option>
                            </select>
                        </div>

                        <div class="botoes-form">
                            <button type="submit" class="btn-vermelho" id="btnSalvarEntregadorEdicao">Salvar alterações</button>
                            <button type="button" class="btn-preto" id="btnCancelarEntregadorEdicao">Cancelar</button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- PEDIDOS -->
            <section id="sec-pedidos" class="secao">
                <h1>Pedidos</h1>
                <p class="subtitulo">Visualização geral dos pedidos do sistema.</p>

                <div class="card-tabela">
                    <h2>Últimos pedidos</h2>
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Endereço</th>
                                <th>Status</th>
                                <th>Criado em</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPedidos">
                            <!-- via JS -->
                        </tbody>
                    </table>
                    <div id="msgPedidos" class="mensagem"></div>
                </div>
            </section>

            <!-- LOGS DE ACESSO -->
            <section id="sec-logs" class="secao">
                <h1>Logs de acesso</h1>
                <p class="subtitulo">Quem entrou no sistema e quando.</p>

                <div class="card-tabela">
                    <h2>Últimos acessos</h2>
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>Tipo</th>
                                <th>Data/Hora</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyLogs">
                            <!-- via JS -->
                        </tbody>
                    </table>
                    <div id="msgLogs" class="mensagem"></div>
                </div>
            </section>

        </main>

    </div>

    <script src="js/admin.js"></script>
</body>

</html>
