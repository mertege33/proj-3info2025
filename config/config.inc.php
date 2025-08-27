<?php

define('USUARIO', 'root'); /// usuario de conexão com o banco
define('SENHA', ''); // senha de conexão com o banco
define('HOST', 'localhost'); // ip do servidor do banco
define('PORT', '3306'); // porta do mysql
define('DB', 'BioLineage'); // nome do banco
define('DSN', "mysql:host=".HOST.";port=".PORT.";dbname=".DB.";charset=UTF8");

try {
    $pdo = new PDO(DSN, USUARIO, SENHA);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}