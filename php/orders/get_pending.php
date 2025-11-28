<?php
require_once '../conexao.php';

try {
    $sql = $pdo->query("
        SELECT 
            p.id,
            u.nome_completo AS cliente,
            p.endereco_entrega,
            p.numero,
            p.complemento,
            p.status
        FROM pedidos p
        INNER JOIN usuarios u ON u.id = p.cliente_id
        WHERE p.status IN ('pendente', 'aceito', 'a_caminho')
        ORDER BY p.id DESC
    ");

    $pedidos = [];

    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

        // monta endereço completo bonito
        $end = $row['endereco_entrega'];

        if (!empty($row['numero'])) {
            $end .= ", " . $row['numero'];
        }

        if (!empty($row['complemento'])) {
            $end .= " - " . $row['complemento'];
        }

        $pedidos[] = [
            'id'      => (int)$row['id'],
            'cliente' => $row['cliente'],
            'endereco'=> $end,
            'status'  => $row['status']
        ];
    }

    echo json_encode([
        'ok'      => true,
        'pedidos' => $pedidos
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'ok'  => false,
        'msg' => 'Erro ao buscar pedidos'
    ]);
}
