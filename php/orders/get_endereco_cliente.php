<?php
session_start();
require "../conexao.php";

$id = $_SESSION['usuario_id'];

$sql = $pdo->prepare("SELECT cep, endereco, numero, complemento FROM usuarios WHERE id = ?");
$sql->execute([$id]);

echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
