<?php
// php/orders/criar_pedido.php
session_start();
require_once "../conexao.php";

// Sempre bom garantir que o PDO está em modo exceção
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// -----------------------------
// 1) Verifica sessão
// -----------------------------
if (!isset($_SESSION['usuario']['id'])) {
    echo json_encode([
        "ok" => false,
        "mensagem" => "Usuário não autenticado (sessão vazia em criar_pedido.php)"
    ]);
    exit;
}

$cliente_id = $_SESSION['usuario']['id'];

// -----------------------------
// 2) Lê JSON enviado
// -----------------------------
$raw = file_get_contents("php://input");
$dados = json_decode($raw, true);

if (!is_array($dados)) {
    echo json_encode([
        "ok" => false,
        "mensagem" => "JSON inválido recebido em criar_pedido.php"
    ]);
    exit;
}

$itens    = $dados["itens"]    ?? [];
$endereco = $dados["endereco"] ?? null;

if (empty($itens) || !$endereco) {
    echo json_encode([
        "ok" => false,
        "mensagem" => "Dados de itens ou endereço não enviados"
    ]);
    exit;
}

// -----------------------------
// 3) Gera token de verificação
// -----------------------------
$token = rand(100000, 999999);

// -----------------------------
// 4) INSERT no banco (com try/catch)
// -----------------------------
try {

    // Transação para garantir consistência
    $pdo->beginTransaction();

    // IMPORTANTE: usar os NOMES que existem na sua tabela pedidos
    // (id, cliente_id, entregador_id, cep, endereco_entrega, numero,
    //  complemento, token_verificacao, status, created_at)
    $sql = $pdo->prepare("
        INSERT INTO pedidos 
        (cliente_id, cep, endereco_entrega, numero, complemento, token_verificacao, status)
        VALUES (?,?,?,?,?,?,?)
    ");

    $sql->execute([
        $cliente_id,
        $endereco['cep']        ?? null,
        $endereco['rua']        ?? null,  // vem do JS como 'rua'
        $endereco['numero']     ?? null,
        $endereco['complemento']?? null,
        $token,
        'pendente'
    ]);

    $pedido_id = $pdo->lastInsertId();

    // Agora, inserir os itens do pedido
    // ATENÇÃO: precisa existir a tabela 'pedido_itens'
    // com pelo menos: id, pedido_id, produto_id, valor
    $stmtItem = $pdo->prepare("
        INSERT INTO pedido_itens (pedido_id, produto_id, valor)
        VALUES (?,?,?)
    ");

    foreach ($itens as $item) {
        $stmtItem->execute([
            $pedido_id,
            $item['id'],
            $item['preco']
        ]);
    }

    $pdo->commit();

    echo json_encode([
        "ok"    => true,
        "token" => $token
    ]);
} catch (PDOException $e) {

    // Desfaz tudo se der ruim
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Loga no servidor
    error_log("ERRO SQL PEDIDO: " . $e->getMessage());

    // Manda pro front o erro explícito pra gente enxergar
    echo json_encode([
        "ok"       => false,
        "mensagem" => "ERRO SQL PEDIDO: " . $e->getMessage()
    ]);
}
