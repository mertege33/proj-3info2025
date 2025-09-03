-- CRIA O BANCO
CREATE SCHEMA IF NOT EXISTS BioLineage;
USE BioLineage;

-- ==========================
-- TABELA USUÁRIO
-- ==========================
CREATE TABLE IF NOT EXISTS usuario (
  id_usuario INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(45) NOT NULL,
  email VARCHAR(45) NULL,
  telefone VARCHAR(45) NULL,
  senha VARCHAR(45) NULL,
  dataNascimento DATE NULL,
  instituicao VARCHAR(45) NULL,
  descricao VARCHAR(45) NULL
);

-- ==========================
-- TABELA DOENÇA
-- ==========================
CREATE TABLE IF NOT EXISTS doenca (
  id_doenca INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(45) NOT NULL
);

-- Inserindo pelo menos 1 doença para evitar erro de FK
INSERT INTO doenca (nome) VALUES ('Nenhuma');

-- ==========================
-- TABELA PERFIL
-- ==========================
CREATE TABLE IF NOT EXISTS perfil (
  id_perfil INT PRIMARY KEY AUTO_INCREMENT,
  sexo ENUM('Feminino','Masculino','Não Binário','Outro') NOT NULL,
  cor_olho ENUM('Azul','Castanho','Cinza','Preto','Verde') NOT NULL,
  cor_cabelo ENUM('Branco','Castanho','Loiro','Preto','Ruivo') NOT NULL,
  tipo_orelha ENUM ('Com divisão','Sem divisão') NULL,
  tipo_sanguineo ENUM('A','B','AB','O') NULL,
  daltonismo ENUM('Sim','Não') NULL,
  sardas ENUM('Sim','Não') NULL,
  fator ENUM('+','-') NULL,
  cov_queixo TINYINT NULL,
  cov_bochecha TINYINT NULL,
  albinismo TINYINT NULL,
  nacionalidade VARCHAR(45) NULL,
  doenca_genealogica INT NOT NULL,
  usuario_idusuario INT NOT NULL,
  id_pai INT NOT NULL,
  id_mae INT NOT NULL,
  FOREIGN KEY (usuario_idusuario) REFERENCES usuario (id_usuario) ON DELETE NO ACTION ON UPDATE NO ACTION,
  FOREIGN KEY (id_pai) REFERENCES usuario (id_usuario) ON DELETE NO ACTION ON UPDATE NO ACTION,
  FOREIGN KEY (id_mae) REFERENCES usuario (id_usuario) ON DELETE NO ACTION ON UPDATE NO ACTION,
  FOREIGN KEY (doenca_genealogica) REFERENCES doenca (id_doenca) ON DELETE NO ACTION ON UPDATE NO ACTION
);

-- ==========================
-- DADOS DE TESTE
-- ==========================

-- Usuário principal
INSERT INTO usuario VALUES (NULL, 'Nome teste','emailteste@mail.com', '12345678','123','2000-06-23','IFC','usuario teste');

-- Pais do usuário principal
INSERT INTO usuario VALUES (NULL, 'Pai teste','paiteste@mail.com', '12345678','123','1975-01-15','IFC','usuario teste');
INSERT INTO usuario VALUES (NULL, 'Mae teste','maeteste@mail.com', '12345678','123','1980-05-25','IFC','usuario teste');

-- Cadastro do perfil principal
INSERT INTO perfil VALUES (NULL, 'Masculino','Castanho','Castanho','Sem divisão','A','Não','Não','+',0,0,0,'Brasileira',1,1,2,3);

-- Avós
INSERT INTO usuario VALUES (NULL, 'Mãe Mãe teste','maemaeteste@mail.com', '12345678','123','1975-01-15','IFC','usuario teste');
INSERT INTO usuario VALUES (NULL, 'Pai Mãe teste','paimaeteste@mail.com', '12345678','123','1975-01-15','IFC','usuario teste');
INSERT INTO usuario VALUES (NULL, 'Mãe Pai teste','maepaiteste@mail.com', '12345678','123','1975-01-15','IFC','usuario teste');
INSERT INTO usuario VALUES (NULL, 'Pai Pai teste','paipaiteste@mail.com', '12345678','123','1975-01-15','IFC','usuario teste');

-- Perfis dos pais (ligando com os avós)
INSERT INTO perfil VALUES (NULL, 'Masculino','Azul','Loiro','Sem divisão','A','Não','Não','+',0,0,0,'Brasileira',1,2,7,6);
INSERT INTO perfil VALUES (NULL, 'Feminino','Castanho','Castanho','Sem divisão','A','Não','Não','+',0,0,0,'Brasileira',1,3,5,4);

-- ==========================
-- CONSULTAS DE TESTE
-- ==========================

-- Consulta trazendo nome + pais
SELECT u.id_usuario,
       u.nome,
       pe.id_perfil,
       (SELECT m.nome FROM usuario m WHERE m.id_usuario = pe.id_mae) AS Mae,
       (SELECT p.nome FROM usuario p WHERE p.id_usuario = pe.id_pai) AS Pai
FROM usuario u
INNER JOIN perfil pe ON (pe.usuario_idusuario = u.id_usuario);

-- Outra forma (mais otimizada com JOINs)
SELECT u.id_usuario,
       u.nome,
       pe.id_perfil,
       mae.nome AS mae,
       pai.nome AS pai
FROM usuario u
INNER JOIN perfil pe ON (pe.usuario_idusuario = u.id_usuario)
LEFT JOIN usuario mae ON (mae.id_usuario = pe.id_mae)
LEFT JOIN usuario pai ON (pai.id_usuario = pe.id_pai);
