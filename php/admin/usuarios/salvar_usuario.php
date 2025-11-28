<?php
session_start();
require_once __DIR__ . "/../../conexao.php";

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    echo json_encode(["ok" => false, "mensagem" => "Acesso negado."]);
    exit;
}

header("Content-Type: application/json; charset=utf-8");

$dados = json_decode(file_get_contents("php://input"), true);

$id       = $dados['id'] ?? null;
$nome     = trim($dados['nome'] ?? "");
$email    = trim($dados['email'] ?? "");
$cpf      = trim($dados['cpf'] ?? "");
$veiculo  = trim($dados['veiculo'] ?? "");

if (!$id || $nome === "" || $email === "") {
    echo json_encode(["ok" => false, "mensagem" => "Dados incompletos."]);
    exit;
}

try {

    if ($veiculo !== "") {
        // ENTREGADOR
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET nome_completo=?, email=?, veiculo=? 
            WHERE id=?
        ");
        $stmt->execute([$nome, $email, $veiculo, $id]);

    } else {
        // CLIENTE
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET nome_completo=?, email=?, cpf=? 
            WHERE id=?
        ");
        $stmt->execute([$nome, $email, $cpf, $id]);
    }

    echo json_encode(["ok" => true, "mensagem" => "Usuário atualizado com sucesso."]);

} catch (Exception $e) {
    echo json_encode(["ok" => false, "mensagem" => $e->getMessage()]);
}
