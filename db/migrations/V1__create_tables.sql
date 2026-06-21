-- V1: Criação das tabelas do sistema de receitas

CREATE TABLE IF NOT EXISTS usuario (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(255) NOT NULL,
    login      VARCHAR(100) NOT NULL UNIQUE,
    senha      VARCHAR(255) NOT NULL,
    situacao   ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS receita (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(255)   NOT NULL,
    descricao     TEXT           NOT NULL,
    data_registro DATE           NOT NULL,
    custo         DECIMAL(10,2)  NOT NULL,
    tipo_receita  ENUM('doce','salgada') NOT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
