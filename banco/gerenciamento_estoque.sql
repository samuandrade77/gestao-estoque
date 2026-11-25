SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS gerenciamento_estoque
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE gerenciamento_estoque;
CREATE TABLE IF NOT EXISTS usuarios(
id_usuario INT auto_increment primary key,
nome VARCHAR(100) NOT NULL,
email VARCHAR(100) NOT NULL UNIQUE,
senha VARCHAR(255) NOT NULL,
perfil VARCHAR(20) NOT NULL
);
DESCRIBE usuarios;
SHOW CREATE TABLE usuarios;

CREATE TABLE IF NOT EXISTS fornecedores (
id_fornecedor INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(100) NOT NULL,
cnpj VARCHAR(18) NOT NULL,
telefone VARCHAR(20) NOT NULL,
email VARCHAR(150) NOT NULL,
endereco VARCHAR(200) NOT NULL,
CONSTRAINT uk_fornecedores_cnpj UNIQUE (cnpj)
);
DESCRIBE fornecedores;
SHOW CREATE TABLE fornecedores;

CREATE TABLE IF NOT EXISTS produtos (
id_produto INT AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(100) NOT NULL,
descricao VARCHAR(255) NOT NULL,
preco DECIMAL(10,2) NOT NULL,
quantidade_estoque INT NOT NULL,
id_fornecedor INT NOT NULL,
FOREIGN KEY (id_fornecedor)
  REFERENCES fornecedores(id_fornecedor),
CONSTRAINT cnk_produtos_preco CHECK (preco >= 0),
CONSTRAINT cnk_produtos_estoque CHECK (quantidade_estoque >= 0)
);
DESCRIBE produtos;
SHOW CREATE TABLE produtos;

CREATE TABLE IF NOT EXISTS clientes (
id_cliente INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(100) NOT NULL,
telefone VARCHAR(20) NOT NULL,
email VARCHAR(150),
endereco VARCHAR(200) NOT NULL
);
DESCRIBE clientes;
SHOW CREATE TABLE clientes;

CREATE TABLE IF NOT EXISTS pedidos (
id_pedido INT AUTO_INCREMENT PRIMARY KEY,
id_cliente INT NOT NULL,
FOREIGN KEY (id_cliente)
  REFERENCES clientes(id_cliente),
id_usuario INT NOT NULL,
FOREIGN KEY (id_usuario)
  REFERENCES usuarios(id_usuario),
data_pedido DATETIME NOT NULL,
situacao VARCHAR(20) NOT NULL,
CONSTRAINT chk_pedidos_situacao
  CHECK (situacao IN ('Pendente', 'Em separação', 'Concluído', 'Cancelado'))
);
        
DESCRIBE pedidos;
SHOW CREATE TABLE pedidos;

CREATE TABLE IF NOT EXISTS itens_pedido(
id_item INT AUTO_INCREMENT PRIMARY KEY,
id_pedido INT NOT NULL,
FOREIGN KEY (id_pedido)
   REFERENCES pedidos (id_pedido),
id_produto INT NOT NULL,
FOREIGN KEY (id_produto)
  REFERENCES produtos (id_produto),
quantidade_solicitada INT NOT NULL,
quantidade_separada INT NOT NULL,
preco_unitario DECIMAL(10,2) NOT NULL,
CONSTRAINT chk_item_quantidades CHECK (
  quantidade_solicitada > 0
  AND quantidade_separada >= 0
  AND quantidade_separada <= quantidade_solicitada
),
CONSTRAINT chk_item_preco CHECK (preco_unitario >= 0)
);
DESCRIBE itens_pedido;
SHOW CREATE TABLE itens_pedido;

CREATE TABLE IF NOT EXISTS movimentacoes_estoque(
id_movimentacao INT AUTO_INCREMENT PRIMARY KEY,
id_produto INT NOT NULL,
FOREIGN KEY (id_produto)
  REFERENCES produtos (id_produto),
id_usuario INT NOT NULL,
FOREIGN KEY (id_usuario)
  REFERENCES usuarios (id_usuario),
id_pedido INT,
FOREIGN KEY (id_pedido)
 REFERENCES pedidos (id_pedido),
tipo VARCHAR(10) NOT NULL,
quantidade INT NOT NULL,
data_movimentacao DATETIME NOT NULL,
observacao VARCHAR(255),
CONSTRAINT chk_movimentacao_quantidade CHECK (quantidade > 0),
CONSTRAINT chk_movimentacao_tipo CHECK (tipo IN ('Entrada', 'Saída'))
);
DESCRIBE movimentacoes_estoque;
SHOW CREATE TABLE movimentacoes_estoque;

SHOW TABLES;

-- =========================================================
-- Usuario do banco usado pela aplicacao (ver conexao.php)
-- =========================================================
CREATE USER IF NOT EXISTS 'estoque_app'@'localhost' IDENTIFIED BY 'Estoque@123';
CREATE USER IF NOT EXISTS 'estoque_app'@'127.0.0.1' IDENTIFIED BY 'Estoque@123';
GRANT SELECT, INSERT, UPDATE, DELETE ON gerenciamento_estoque.* TO 'estoque_app'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON gerenciamento_estoque.* TO 'estoque_app'@'127.0.0.1';

-- =========================================================
-- Dados iniciais
-- Login do sistema: admin@estoque.com  /  senha: admin123
-- =========================================================
INSERT IGNORE INTO usuarios (nome, email, senha, perfil) VALUES
('Administrador', 'admin@estoque.com', '$2y$10$iuCJMt83UoijPIfMkD5eyeoWjYmwX4rr1HKOJFwL7jkxRZDEup8Dq', 'Administrador');
SET @admin = (SELECT id_usuario FROM usuarios WHERE email = 'admin@estoque.com');

INSERT IGNORE INTO fornecedores (id_fornecedor, nome, cnpj, telefone, email, endereco) VALUES
(1, 'Distribuidora Triangulo', '11222333000181', '3432100000', 'vendas@triangulo.example', 'Av. Joao Naves de Avila, 1000 - Uberlandia/MG');

INSERT IGNORE INTO produtos (id_produto, nome, descricao, preco, quantidade_estoque, id_fornecedor) VALUES
(1, 'Caixa de Papelao', 'Caixa 40x30x20 cm para embalagem', 4.50, 200, 1),
(2, 'Fita Adesiva', 'Rolo transparente 48 mm x 100 m', 7.90, 80, 1);

INSERT IGNORE INTO clientes (id_cliente, nome, telefone, email, endereco) VALUES
(1, 'Mercado Central', '34999990000', 'compras@mercadocentral.example', 'Rua das Flores, 50 - Uberlandia/MG');

INSERT IGNORE INTO pedidos (id_pedido, id_cliente, id_usuario, data_pedido, situacao) VALUES
(1, 1, @admin, NOW(), 'Pendente');

INSERT IGNORE INTO itens_pedido (id_item, id_pedido, id_produto, quantidade_solicitada, quantidade_separada, preco_unitario) VALUES
(1, 1, 1, 50, 0, 4.50),
(2, 1, 2, 20, 0, 7.90);

INSERT IGNORE INTO movimentacoes_estoque (id_movimentacao, id_produto, id_usuario, id_pedido, tipo, quantidade, data_movimentacao, observacao) VALUES
(1, 1, @admin, NULL, 'Entrada', 200, NOW(), 'Estoque inicial'),
(2, 2, @admin, NULL, 'Entrada', 80, NOW(), 'Estoque inicial');
