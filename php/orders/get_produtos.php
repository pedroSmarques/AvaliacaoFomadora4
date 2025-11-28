<?php
require_once "../conexao.php";

// Busca apenas produtos ativos
$sql = $pdo->query("SELECT id, nome, preco FROM produtos WHERE ativo = 1 ORDER BY nome ASC");
$rows = $sql->fetchAll(PDO::FETCH_ASSOC);

// Normaliza o formato
$produtos = array_map(function($p) {
    return [
        "id"    => (int)$p["id"],
        "nome"  => $p["nome"],
        "preco" => (float)$p["preco"]
    ];
}, $rows);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($produtos);
