create schema BioLineage;

CREATE TABLE IF NOT EXISTS usuario (
  id_usuario INT NOT NULL,
  nome VARCHAR(45) NOT NULL,
  email VARCHAR(45) NULL,
  telefone VARCHAR(45) NULL,
  senha VARCHAR(45) NULL,
  dataNascimento VARCHAR(45) NULL,
  Instituicao VARCHAR(45) NULL,
  descricao VARCHAR(45) NULL,
  PRIMARY KEY (id_usuario));


CREATE TABLE IF NOT EXISTS perfil (
  id_perfil INT NOT NULL,
  nome VARCHAR(45) NOT NULL,
  cor_olho VARCHAR(45) NOT NULL,
  cor_cabelo VARCHAR(45) NOT NULL,
  tipo_orelha VARCHAR(45) NULL,
  tipo_sanguineo VARCHAR(45) NULL,
  cov_queixo VARCHAR(45) NULL,
  cov_bochecha VARCHAR(45) NULL,
  nacionalidade VARCHAR(45) NULL,
  doenca_genealogica VARCHAR(45) NULL,
  usuariocol VARCHAR(45) NOT NULL,
  usuariocol1 VARCHAR(45) NULL,
  usuario_idusuario INT NOT NULL,
  id_pai INT NOT NULL,
  id_mae INT NOT NULL,
  PRIMARY KEY (id_perfil),

    FOREIGN KEY (usuario_idusuario)
    REFERENCES usuario (id_usuario)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
    FOREIGN KEY (id_pai)
    REFERENCES usuario (id_usuario)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
    FOREIGN KEY (id_mae)
    REFERENCES usuario (id_usuario)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);