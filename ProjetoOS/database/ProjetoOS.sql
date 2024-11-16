CREATE DATABASE ProjetoOS;
USE ProjetoOS;

CREATE TABLE tbl_cliente (
    id INT AUTO_INCREMENT UNIQUE PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    endereco VARCHAR(255),
    numero VARCHAR(10)
);

CREATE TABLE tbl_produto (
    id INT AUTO_INCREMENT UNIQUE PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    ativo TINYINT(1) DEFAULT 0
);

CREATE TABLE tbl_os (
    os INT AUTO_INCREMENT UNIQUE PRIMARY KEY,
    data_abertura DATE NOT NULL,
    nome_consumidor VARCHAR(255) NOT NULL,
    cpf_consumidor VARCHAR(14) NOT NULL,
    produto_id INT,
    cliente_id INT,
    finalizada TINYINT(1) DEFAULT 0,
    FOREIGN KEY (produto_id) REFERENCES tbl_produto(id),
    FOREIGN KEY (cliente_id) REFERENCES tbl_cliente(id)
);
