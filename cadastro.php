<?php
// cadastro.php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastro - Delivery Universitário</title>
    <link rel="stylesheet" href="css/cadastro.css">
</head>

<body>

    <main class="container-cadastro">

        <section class="card-cadastro">

            <h1>Crie sua Conta</h1>
            <p class="subtitulo">Selecione o tipo de usuário</p>

            <!-- FORM INICIA AQUI -->
            <form id="formCadastro" method="POST" action="php/inserir_usuario.php" novalidate>

                <!-- SELECT TIPO DE USUÁRIO DENTRO DO FORM -->
                <div class="grupo-input">
                    <label for="tipo_usuario">Tipo de Conta</label>
                    <select id="tipo_usuario" name="tipo_usuario" class="select-tipo">
                        <option value="" disabled selected>Selecione...</option>
                        <option value="cliente">Cliente</option>
                        <option value="entregador">Entregador</option>
                    </select>
                    <small class="erro-campo" id="erroTipo"></small>
                </div>

                <!-- CAMPOS COMUNS -->
                <div class="grupo-input">
                    <label>Nome Completo</label>
                    <input type="text" id="nome" name="nome">
                    <small class="erro-campo" id="erroNome"></small>
                </div>

                <div class="grupo-input">
                    <label>CPF</label>
                    <input type="text" id="cpf" name="cpf" maxlength="14">
                    <small class="erro-campo" id="erroCpf"></small>
                </div>

                <div class="grupo-input">
                    <label>Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento">
                    <small class="erro-campo" id="erroData"></small>
                </div>

                <div class="grupo-input">
                    <label>E-mail</label>
                    <input type="email" id="email" name="email">
                    <small class="erro-campo" id="erroEmail"></small>
                </div>

                <div class="grupo-input">
                    <label>Senha</label>
                    <input type="password" id="senha" name="senha">
                    <small class="erro-campo" id="erroSenha"></small>
                </div>

                <div class="grupo-input">
                    <label>Confirmar Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha">
                    <small class="erro-campo" id="erroConfirmar"></small>
                </div>

                <!-- ENDEREÇO -->
                <hr class="divisor">

                <div class="grupo-input">
                    <label>CEP</label>
                    <input type="text" id="cep" name="cep" maxlength="9">
                    <small class="erro-campo" id="erroCep"></small>
                </div>

                <div class="grupo-input">
                    <label>Endereço (Rua)</label>
                    <input type="text" id="endereco" name="endereco" readonly>
                    <small class="erro-campo" id="erroEndereco"></small>
                </div>

                <div class="grupo-linha">
                    <div class="grupo-input">
                        <label>Número</label>
                        <input type="text" id="numero" name="numero">
                    </div>
                    <div class="grupo-input">
                        <label>Complemento</label>
                        <input type="text" id="complemento" name="complemento">
                    </div>
                </div>

                <!-- CAMPOS EXTRAS SOMENTE PARA ENTREGADOR -->
                <div id="camposEntregador" class="hidden">

                    <hr class="divisor">

                    <div class="grupo-input">
                        <label>RG</label>
                        <input type="text" id="rg" name="rg" maxlength="12">
                        <small class="erro-campo" id="erroRg"></small>
                    </div>

                    <div class="grupo-input">
                        <label>Tipo de Veículo</label>
                        <select id="veiculo" name="veiculo">
                            <option value="" disabled selected>Selecione...</option>
                            <option value="moto">Moto</option>
                            <option value="carro">Carro</option>
                            <option value="bicicleta">Bicicleta</option>
                        </select>
                        <small class="erro-campo" id="erroVeiculo"></small>
                    </div>

                    <!-- CAMPOS APARECEM APENAS SE ESCOLHER MOTO OU CARRO -->
                    <div id="camposVeiculoMotorizado" class="hidden">

                        <div class="grupo-input">
                            <label>Modelo da Moto/Carro</label>
                            <input type="text" id="modelo" name="modelo">
                            <small class="erro-campo" id="erroModelo"></small>
                        </div>

                        <div class="grupo-input">
                            <label>Placa (Padrão Mercosul)</label>
                            <input type="text" id="placa" name="placa" maxlength="7">
                            <small class="erro-campo" id="erroPlaca"></small>
                        </div>

                    </div>

                </div>

                <!-- BOTÃO SUBMIT -->
                <button type="submit" class="btn-vermelho">Registrar</button>

                <p class="link-login">
                    Já tem conta? <a href="login.php">Entrar</a>
                </p>

            </form> <!-- FECHA FORM AQUI -->

        </section>

    </main>

    <script src="js/cadastro.js"></script>
</body>

</html>