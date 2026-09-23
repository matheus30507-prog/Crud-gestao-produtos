<?php 
$servidor = "localhost"; 
$usuario = "root"; 
$senhaBanco = "";

try {
	$pdo = new PDO("mysql:host=$servidor", $usuario, $senhaBanco);

	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$pdo->exec("CREATE DATABASE IF NOT EXISTS gestao_produtos");

	$pdo->exec("USE gestao_produtos");
}



?>