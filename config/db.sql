CREATE TABLE IF NOT EXISTS `mydb`.`usuario` (
  `idusuario` INT NOT NULL,
  `nome` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NULL,
  `telefone` VARCHAR(45) NULL,
  `senha` VARCHAR(45) NULL,
  `dataNascimento` VARCHAR(45) NULL,
  `Instituicao` VARCHAR(45) NULL,
  `descricao` VARCHAR(45) NULL,
  PRIMARY KEY (`idusuario`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`perfil`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`perfil` (
  `id` INT NOT NULL,
  `nome` VARCHAR(45) NOT NULL,
  `cor_olho` VARCHAR(45) NOT NULL,
  `cor_cabelo` VARCHAR(45) NOT NULL,
  `tipo_sangue` VARCHAR(45) NOT NULL,
  `usuariocol` VARCHAR(45) NOT NULL,
  `usuariocol1` VARCHAR(45) NULL,
  `usuario_idusuario` INT NOT NULL,
  `id_pai` INT NOT NULL,
  `id_mae` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_perfil_usuario_idx` (`usuario_idusuario` ASC) VISIBLE,
  INDEX `fk_perfil_usuario1_idx` (`id_pai` ASC) VISIBLE,
  INDEX `fk_perfil_usuario2_idx` (`id_mae` ASC) VISIBLE,
  CONSTRAINT `fk_perfil_usuario`
    FOREIGN KEY (`usuario_idusuario`)
    REFERENCES `mydb`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_usuario1`
    FOREIGN KEY (`id_pai`)
    REFERENCES `mydb`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_usuario2`
    FOREIGN KEY (`id_mae`)
    REFERENCES `mydb`.`usuario` (`idusuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '			';


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
