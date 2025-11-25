<?php
// Configurações básicas do banco de dados MySQL
// Atualize conforme seu ambiente local

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'safeshare';

/*
Script SQL para criar o banco de dados e tabelas:

CREATE DATABASE safeshare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE safeshare;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE cofres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    id_dono INT NOT NULL,
    FOREIGN KEY (id_dono) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE membros_cofre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cofre INT NOT NULL,
    id_usuario INT NOT NULL,
    papel ENUM('OWNER','EDITOR','VIEWER') NOT NULL,
    FOREIGN KEY (id_cofre) REFERENCES cofres(id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE senhas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cofre INT NOT NULL,
    rotulo VARCHAR(100) NOT NULL,
    usuario_login VARCHAR(100) NOT NULL,
    senha_valor VARCHAR(255) NOT NULL,
    notas TEXT,
    FOREIGN KEY (id_cofre) REFERENCES cofres(id)
) ENGINE=InnoDB;

CREATE TABLE etiquetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE senha_etiqueta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_senha INT NOT NULL,
    id_etiqueta INT NOT NULL,
    FOREIGN KEY (id_senha) REFERENCES senhas(id),
    FOREIGN KEY (id_etiqueta) REFERENCES etiquetas(id)
) ENGINE=InnoDB;
*/
?>
