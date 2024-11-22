-- Criação do banco de dados
CREATE DATABASE sistema_atendimento;

-- Uso do banco de dados
USE sistema_atendimento;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    cpf_cnpj VARCHAR(20) NOT NULL,
    whatsapp VARCHAR(20),
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    data_nascimento DATE,
    senha VARCHAR(255) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de histórico de atendimentos
CREATE TABLE historico_atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_retorno DATE,
    descricao TEXT NOT NULL,
    comentarios TEXT NOT NULL,
    pagamento VARCHAR(50) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Verificação de integridade (opcional)
-- Testar se as chaves estrangeiras estão funcionando
SET FOREIGN_KEY_CHECKS = 1;
