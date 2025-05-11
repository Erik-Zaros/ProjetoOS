CREATE DATABASE ProjetoOS;
USE ProjetoOS;

CREATE TABLE tbl_cliente (
    cliente SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    cep VARCHAR(10),
    endereco VARCHAR(255),
    bairro VARCHAR(255),
    numero VARCHAR(10),
    cidade VARCHAR(255),
    estado VARCHAR(10)
);

CREATE TABLE tbl_produto (
    produto SERIAL PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT FALSE
);

CREATE TABLE tbl_os (
    os SERIAL PRIMARY KEY,
    data_abertura DATE NOT NULL,
    nome_consumidor VARCHAR(255) NOT NULL,
    cpf_consumidor VARCHAR(14) NOT NULL,
    produto_id INTEGER REFERENCES tbl_produto(id),
    cliente_id INTEGER REFERENCES tbl_cliente(id),
    finalizada BOOLEAN DEFAULT FALSE
);
