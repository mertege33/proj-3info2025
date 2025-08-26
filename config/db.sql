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
  
  
     
  PRIMARY KEY (idusuario));



CREATE TABLE IF NOT EXISTS perfil (
  sexo VARCHAR(45) NOT NULL,
  id_perfil INT NOT NULL,
  nome VARCHAR(45) NOT NULL,
  cor_olho VARCHAR(45) NOT NULL,
  cor_cabelo VARCHAR(45) NOT NULL,
  daltonismo VARCHAR(45) NULL,
  sardas VARCHAR(45) NULL,
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

  CONSTRAINT fk_perfil_usuario
    FOREIGN KEY (usuario_id_usuario)
    REFERENCES mydb.usuario (id_usuario)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_perfil_usuario1
    FOREIGN KEY (id_pai)
    REFERENCES mydb.usuario (id_usuario)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_perfil_usuario2
    FOREIGN KEY (id_mae)
    REFERENCES mydb.usuario (id_usuario)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
