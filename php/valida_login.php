<?php
session_start();
require_once __DIR__ . '/conexao.php';

// pega dados
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// valida vazio
if ($email === '' || $senha === '') {
    header("Location: ../login.php?erro=credenciais");
    exit;
}

// buscar usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {

    // cria contador de tentativas se não existir
    if (!isset($_SESSION['tentativas'])) {
        $_SESSION['tentativas'] = 0;
    }

    $_SESSION['tentativas']++;

    // mais de 3 erros → redireciona p/ cadastro
    if ($_SESSION['tentativas'] >= 3) {
        $_SESSION['tentativas'] = 0; // zera
        header("Location: ../cadastro.php?erro=bloqueado");
        exit;
    }

    header("Location: ../login.php?erro=credenciais");
    exit;
}

// SE CHEGOU AQUI → senha certa
$_SESSION['tentativas'] = 0;

// SESSÃO CORRETA
$_SESSION['usuario'] = [
    'id'   => $usuario['id'],
    'nome' => $usuario['nome_completo'],
    'role' => $usuario['role']
];

/* 🔥 AQUI REGISTRA O LOG DE ACESSO */
require_once __DIR__ . "/admin/logs/registrar_log.php";

/* 🔥 Agora sim redireciona */
if ($usuario['role'] === 'admin') {
    header("Location: ../admin.php");
    exit;
}

if ($usuario['role'] === 'entregador') {
    header("Location: ../entregador.php");
    exit;
}

// CLIENTE
header("Location: ../sistema.php");
exit;
