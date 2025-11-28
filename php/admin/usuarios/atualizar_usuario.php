<?php
// php/admin/usuarios/atualizar_usuario.php
session_start();
require_once __DIR__ . "/../../conexao.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    echo json_encode(["ok" => false, "mensagem" => "Acesso negado."]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$id    = $input['id']    ?? "";
$role  = $input['role']  ?? "";
$nome  = trim($input['nome']  ?? "");
$email = trim($input['email'] ?? "");

if ($id === "" || $role === "" || $nome === "" || $email === "") {
    echo json_encode(["ok" => false, "mensagem" => "Dados obrigatórios não informados."]);
    exit;
}

try {

    if ($role === "cliente") {
        $cpf = preg_replace('/\D/', '', $input['cpf'] ?? "");

        $stmt = $pdo->prepare("
            UPDATE usuarios
               SET nome_completo = ?,
                   email         = ?,
                   cpf           = ?
             WHERE id = ?
        ");
        $stmt->execute([$nome, $email, $cpf, $id]);

    } elseif ($role === "entregador") {
        $veiculo = $input['veiculo'] ?? "";

        $stmt = $pdo->prepare("
            UPDATE usuarios
               SET nome_completo = ?,
                   email         = ?,
                   veiculo       = ?
             WHERE id = ?
        ");
        $stmt->execute([$nome, $email, $veiculo, $id]);

    } else {
        echo json_encode(["ok" => false, "mensagem" => "Tipo de usuário inválido."]);
        exit;
    }

    echo json_encode(["ok" => true, "mensagem" => "Usuário atualizado com sucesso."]);

} catch (Exception $e) {
    echo json_encode(["ok" => false, "mensagem" => "Erro ao atualizar usuário: " . $e->getMessage()]);
}
