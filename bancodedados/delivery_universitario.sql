CREATE DATABASE IF NOT EXISTS delivery_universitario
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE delivery_universitario;

-- Tabela de usuários (clientes, entregadores, admin)
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome_completo VARCHAR(100) NOT NULL,
  cpf CHAR(11) NOT NULL UNIQUE,
  rg VARCHAR(20),
  data_nascimento DATE,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  cep VARCHAR(9),
  endereco VARCHAR(150),
  numero VARCHAR(10),
  complemento VARCHAR(100),
  veiculo ENUM('moto','carro','bicicleta','nenhum') DEFAULT 'nenhum',
  placa VARCHAR(10),
  modelo_veiculo VARCHAR(60),
  role ENUM('cliente','entregador','admin') DEFAULT 'cliente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  entregador_id INT DEFAULT NULL,
  cep VARCHAR(9),
  endereco_entrega VARCHAR(150) NOT NULL,
  numero VARCHAR(10),
  complemento VARCHAR(100),
  token_verificacao VARCHAR(10) NOT NULL,
  status ENUM('pendente','aceito','a_caminho','entregue','recusado')
    DEFAULT 'pendente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES usuarios(id),
  FOREIGN KEY (entregador_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Logs de acesso
CREATE TABLE IF NOT EXISTS logs_acesso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  role ENUM('cliente','entregador','admin'),
  ip VARCHAR(45),
  data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Produtos (pra consultas e futuro carrinho)
CREATE TABLE IF NOT EXISTS produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  ativo TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de pedidos 
CREATE TABLE IF NOT EXISTS pedido_itens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  produto_id INT NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_pedido_itens_pedido
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_pedido_itens_produto
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
    ON DELETE CASCADE
);
