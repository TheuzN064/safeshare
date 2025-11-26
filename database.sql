-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS vault_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vault_db;

-- Usuários do sistema
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO usuarios (nome, email) VALUES
('Usuário Demo', 'demo@vault.local');

-- Categorias para classificar logins
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(60) NOT NULL,
    cor_hex CHAR(7) NOT NULL
) ENGINE=InnoDB;

INSERT INTO categorias (nome, cor_hex) VALUES
('Social', '#1da1f2'),
('Bancos', '#bb86fc'),
('Trabalho', '#00c853');

-- Logins armazenados (senha em texto puro apenas para fins didáticos)
CREATE TABLE IF NOT EXISTS logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_categoria INT NOT NULL,
    site_nome VARCHAR(100) NOT NULL,
    site_url VARCHAR(255) DEFAULT NULL,
    login VARCHAR(120) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    CONSTRAINT fk_logins_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE, -- FK: logins.id_usuario -> usuarios.id
    CONSTRAINT fk_logins_categoria FOREIGN KEY (id_categoria) REFERENCES categorias(id) ON DELETE RESTRICT -- FK: logins.id_categoria -> categorias.id
) ENGINE=InnoDB;

-- Cartões de crédito/débito
CREATE TABLE IF NOT EXISTS cartoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    titular VARCHAR(120) NOT NULL,
    numero VARCHAR(25) NOT NULL,
    validade CHAR(5) NOT NULL,
    cvv CHAR(4) NOT NULL,
    bandeira VARCHAR(30) NOT NULL,
    CONSTRAINT fk_cartoes_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE -- FK: cartoes.id_usuario -> usuarios.id
) ENGINE=InnoDB;
