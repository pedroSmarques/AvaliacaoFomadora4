<?php
session_start();
require_once __DIR__ . "/../../conexao.php";

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    echo json_encode(["ok" => false, "mensagem" => "Acesso negado."]);
    exit;
}

header("Content-Type: application/json; charset=utf-8");

$role     = $_GET['role'] ?? null;
$idBusca  = $_GET['id']   ?? null;
$cpf      = $_GET['cpf']  ?? null;
$nome     = $_GET['nome'] ?? null;
$email    = $_GET['email'] ?? null;

try {

    // Buscar 1 usuário específico (editar)
    if ($idBusca) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$idBusca]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            "ok" => $u ? true : false,
            "usuario" => $u,
            "mensagem" => $u ? "" : "Usuário não encontrado."
        ]);
        exit;
    }

    // Montar filtro geral
    $sql = "SELECT * FROM usuarios WHERE 1=1";
    $params = [];

    if ($role) {
        $sql .= " AND role = ?";
        $params[] = $role;
    }

    if ($cpf) {
        $sql .= " AND cpf = ?";
        $params[] = $cpf;
    }

    if ($nome) {
        $sql .= " AND nome_completo LIKE ?";
        $params[] = "%{$nome}%";
    }

    if ($email) {
        $sql .= " AND email LIKE ?";
        $params[] = "%{$email}%";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "ok" => true,
        "usuarios" => $users
    ]);

} catch (Exception $e) {
    echo json_encode(["ok" => false, "mensagem" => $e->getMessage()]);
}
