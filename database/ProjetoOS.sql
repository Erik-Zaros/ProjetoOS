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
    estado VARCHAR(10),
    posto INTEGER REFERENCES tbl_posto(posto),
    data_input TIMESTAMP DEFAULT NOW()
);

CREATE TABLE tbl_produto (
    produto SERIAL PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT FALSE,
    posto INTEGER REFERENCES tbl_posto(posto),
    data_input TIMESTAMP DEFAULT NOW()
);

CREATE TABLE tbl_peca (
    peca SERIAL PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT FALSE,
    posto INTEGER REFERENCES tbl_posto(posto),
    data_input TIMESTAMP DEFAULT NOW()
);

CREATE TABLE tbl_os (
    os SERIAL PRIMARY KEY,
    data_abertura DATE NOT NULL,
    nome_consumidor VARCHAR(255) NOT NULL,
    cpf_consumidor VARCHAR(14) NOT NULL,
    produto INTEGER REFERENCES tbl_produto(produto),
    cliente INTEGER REFERENCES tbl_cliente(cliente),
    finalizada BOOLEAN DEFAULT FALSE,
    posto INTEGER REFERENCES tbl_posto(posto),
    cancelada BOOLEAN DEFAULT FALSE
);

CREATE TABLE tbl_posto (
  posto SERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE tbl_usuario (
  usuario SERIAL PRIMARY KEY,
  login TEXT NOT NULL UNIQUE,
  senha TEXT NOT NULL,
  nome TEXT NOT NULL,
  posto INTEGER REFERENCES tbl_posto(posto),
  ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE tbl_log_auditor (
    log_auditor UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tabela TEXT NOT NULL,
    id_registro TEXT NOT NULL,
    acao TEXT NOT NULL CHECK (acao IN ('insert', 'update', 'delete')),
    antes JSONB,
    depois JSONB,
    usuario INT REFERENCES tbl_usuario(usuario),
    data_log TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tbl_estoque (
    estoque SERIAL PRIMARY KEY,
    qtde INTEGER NOT NULL,
    produto INTEGER REFERENCES tbl_produto(produto),
    peca INTEGER REFERENCES tbl_peca(peca),
    posto INTEGER REFERENCES tbl_posto(posto) NOT NULL,
    data_input TIMESTAMPTZ DEFAULT NOW(),
    CHECK ( (produto IS NOT NULL) <> (peca IS NOT NULL) )
);

CREATE TABLE tbl_estoque_movimento (
    estoque_movimento SERIAL PRIMARY KEY,
    posto INTEGER REFERENCES tbl_posto(posto) NOT NULL,
    produto INTEGER REFERENCES tbl_produto(produto),
    peca INTEGER REFERENCES tbl_peca(peca),
    tipo CHAR(1) NOT NULL CHECK (tipo IN ('E','S')),
    qtde INTEGER NOT NULL CHECK (qtde > 0),
    os BIGINT REFERENCES tbl_os(os),
    motivo TEXT,
    usuario INTEGER,
    data_input TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CHECK ( (produto IS NOT NULL) <> (peca IS NOT NULL) )
);
