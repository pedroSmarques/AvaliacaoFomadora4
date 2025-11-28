<?php
require_once __DIR__ . "/../../conexao.php";

$usuarioId = $_SESSION['usuario']['id'];
$role      = $_SESSION['usuario']['role'];

try {
    $stmt = $pdo->prepare("
        INSERT INTO logs_acesso (usuario_id, role)
        VALUES (?, ?)
    ");
    $stmt->execute([$usuarioId, $role]);

} catch (Exception $e) {
    error_log("ERRO LOG ACESSO: " . $e->getMessage());
}
