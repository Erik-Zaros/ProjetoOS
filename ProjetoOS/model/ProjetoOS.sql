CREATE DATABASE ProjetoOS;
USE ProjetoOS;

CREATE TABLE tbl_cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    endereco VARCHAR(255),
    numero VARCHAR(10)
);


CREATE TABLE tbl_produto (
    codigo VARCHAR(50) PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL,
    ativo TINYINT(1) NOT NULL
);


CREATE TABLE tbl_os (
    os INT AUTO_INCREMENT PRIMARY KEY,
    data_abertura DATE NOT NULL,
    nome_consumidor VARCHAR(255) NOT NULL,
    cpf_consumidor VARCHAR(14) NOT NULL,
    produto_codigo VARCHAR(50),
    cliente_id INT,
    finalizada TINYINT(1) DEFAULT 0,
    FOREIGN KEY (produto_codigo) REFERENCES tbl_produto(codigo),
    FOREIGN KEY (cliente_id) REFERENCES tbl_cliente(id)
);
