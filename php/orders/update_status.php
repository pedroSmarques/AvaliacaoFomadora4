<?php
require_once '../conexao.php';

$data  = json_decode(file_get_contents("php://input"), true);
$id    = isset($data['id']) ? (int)$data['id'] : 0;
$acao  = $data['acao'] ?? '';
$token = $data['token'] ?? null;

if ($id <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
    exit;
}

try {

    if ($acao === 'aceitar') {
        $stmt = $pdo->prepare("UPDATE pedidos SET status = 'aceito' WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['ok' => true]);
        exit;
    }

    if ($acao === 'a_caminho') {
        $stmt = $pdo->prepare("UPDATE pedidos SET status = 'a_caminho' WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['ok' => true]);
        exit;
    }

    if ($acao === 'recusar') {
        $stmt = $pdo->prepare("UPDATE pedidos SET status = 'recusado' WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['ok' => true]);
        exit;
    }

    if ($acao === 'finalizar') {

        if (!$token) {
            echo json_encode(['ok' => false, 'msg' => 'Token obrigatório']);
            exit;
        }

        // Busca token correto no banco
        $busca = $pdo->prepare("SELECT token_verificacao FROM pedidos WHERE id = ?");
        $busca->execute([$id]);
        $pedido = $busca->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            echo json_encode(['ok' => false, 'msg' => 'Pedido não encontrado']);
            exit;
        }

        if ($pedido['token_verificacao'] !== $token) {
            echo json_encode(['ok' => false, 'msg' => 'Token inválido']);
            exit;
        }

        // Token válido → finalizar
        $stmt = $pdo->prepare("UPDATE pedidos SET status = 'entregue' WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['ok' => true]);
        exit;
    }

    echo json_encode(['ok' => false, 'msg' => 'Ação inválida']);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'msg' => 'Erro no servidor']);
}
