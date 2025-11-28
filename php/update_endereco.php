<?php
session_start();
require "../conexao.php";

$id = $_SESSION['usuario_id'];
$dados = json_decode(file_get_contents("php://input"), true);

$sql = $pdo->prepare("
UPDATE usuarios SET cep=?, endereco=?, numero=?, complemento=? WHERE id=?
");

$sql->execute([
    $dados["cep"],
    $dados["rua"],
    $dados["numero"],
    $dados["complemento"],
    $id
]);

echo json_encode(["ok" => true]);
