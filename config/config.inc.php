<?php

define('USUARIO', 'root'); 
define('SENHA', ''); // 
define('HOST', 'localhost');
define('PORT', '3306'); 
define('DB', 'biolineage'); 
define('DSN', "mysql:host=".HOST.";port=".PORT.";dbname=".DB.";charset=UTF8");

try {
    $pdo = new PDO(DSN, USUARIO, SENHA);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}