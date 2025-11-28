<?php
require_once "../php/conexao.php";

// RECEBER DADOS DO FORMULÁRIO --------------------------
$tipo = $_POST['tipo_usuario'] ?? 'cliente'; // <-- nome correto vindo do SELECT

$nome = $_POST['nome'];
$cpf = preg_replace('/\D/', '', $_POST['cpf']);
$data = $_POST['data_nascimento'];
$email = $_POST['email'];
$senhaHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$cep = $_POST['cep'];
$endereco = $_POST['endereco'];
$numero = $_POST['numero'];
$complemento = $_POST['complemento'];

// CAMPOS DE ENTREGADOR
$rg = $_POST['rg'] ?? null;
$veiculo = $_POST['veiculo'] ?? "nenhum";
$modelo = $_POST['modelo'] ?? null;
$placa = $_POST['placa'] ?? null;


// VERIFICA DUPLICADO ------------------------------------
$busca = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? OR cpf = ?");
$busca->execute([$email, $cpf]);

if ($busca->fetch()) {
    header("Location: ../cadastro.php?erro=duplicado");
    exit;
}


// INSERT -------------------------------------------------
$sql = $pdo->prepare("
INSERT INTO usuarios 
(nome_completo, cpf, rg, data_nascimento, email, senha_hash, cep, endereco, numero, complemento, veiculo, modelo_veiculo, placa, role)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$sql->execute([
    $nome,
    $cpf,
    $rg,
    $data,
    $email,
    $senhaHash,
    $cep,
    $endereco,
    $numero,
    $complemento,
    $veiculo,
    $modelo,
    $placa,
    $tipo  // <-- AGORA RECEBE O VALOR CORRETO
]);

header("Location: ../login.php?registrado=1");
exit;
