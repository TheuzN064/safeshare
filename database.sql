-- Criação do banco e tabelas para o SafeShare
CREATE DATABASE IF NOT EXISTS safeshare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE safeshare;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Tabela de cofres
CREATE TABLE IF NOT EXISTS cofres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    id_dono INT NOT NULL,
    FOREIGN KEY (id_dono) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Membros de um cofre com papéis
CREATE TABLE IF NOT EXISTS membros_cofre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cofre INT NOT NULL,
    id_usuario INT NOT NULL,
    papel ENUM('OWNER','EDITOR','VIEWER') NOT NULL,
    FOREIGN KEY (id_cofre) REFERENCES cofres(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Senhas associadas a um cofre
CREATE TABLE IF NOT EXISTS senhas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cofre INT NOT NULL,
    rotulo VARCHAR(100) NOT NULL,
    usuario_login VARCHAR(100) NOT NULL,
    senha_valor VARCHAR(255) NOT NULL,
    notas TEXT,
    FOREIGN KEY (id_cofre) REFERENCES cofres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Etiquetas reutilizáveis
CREATE TABLE IF NOT EXISTS etiquetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Associação senha x etiqueta
CREATE TABLE IF NOT EXISTS senha_etiqueta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_senha INT NOT NULL,
    id_etiqueta INT NOT NULL,
    FOREIGN KEY (id_senha) REFERENCES senhas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_etiqueta) REFERENCES etiquetas(id) ON DELETE CASCADE
) ENGINE=InnoDB;
