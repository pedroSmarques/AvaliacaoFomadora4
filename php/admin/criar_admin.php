<?php
// php/admin/criar_admin.php
// Cria um usuário admin padrão se ainda não existir.

require_once __DIR__ . "/../conexao.php";

try {
    // Verifica se já existe admin com esse e-mail
    $emailAdmin = "admin@admin.com";

    $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $check->execute([$emailAdmin]);
    $existe = $check->fetch();

    if ($existe) {
        echo "Administrador já existe com o e-mail: {$emailAdmin}";
        exit;
    }

    // Dados padrão do admin
    $nome        = "Administrador do Sistema";
    $cpf         = "00000000000";      // apenas placeholder
    $rg          = null;
    $dataNasc    = "2000-01-01";
    $senhaHash   = password_hash("admin123", PASSWORD_DEFAULT);
    $cep         = "00000000";
    $endereco    = "Endereço do Admin";
    $numero      = "0";
    $complemento = "Admin";
    $veiculo     = "nenhum";
    $modelo      = null;
    $placa       = null;
    $role        = "admin";

    $sql = $pdo->prepare("
        INSERT INTO usuarios
        (nome_completo, cpf, rg, data_nascimento, email, senha_hash, cep, endereco, numero, complemento, veiculo, modelo_veiculo, placa, role)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $sql->execute([
        $nome,
        $cpf,
        $rg,
        $dataNasc,
        $emailAdmin,
        $senhaHash,
        $cep,
        $endereco,
        $numero,
        $complemento,
        $veiculo,
        $modelo,
        $placa,
        $role
    ]);

    echo "Administrador criado com sucesso!<br>";
    echo "E-mail: {$emailAdmin}<br>";
    echo "Senha: admin123<br>";
    echo "Por segurança, remova este arquivo depois de usar.";
} catch (Exception $e) {
    echo "Erro ao criar admin: " . $e->getMessage();
}
