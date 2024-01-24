<?php
// Configurações do banco de dados
$servername = "localhost"; // Nome do servidor do banco de dados (geralmente 'localhost' em ambiente de desenvolvimento)
$username = "root"; // Nome de usuário do banco de dados
$password = "RY1mR3W-8*Gm"; // Senha do banco de dados
$database = "draper"; // Nome do banco de dados

// Criar uma conexão com o banco de dados
$mysqli = new mysqli($servername, $username, $password, $database);

// Verificar a conexão
if ($mysqli->connect_error) {
    die("Erro na conexão com o banco de dados: " . $mysqli->connect_error);
}

// Defina o conjunto de caracteres para UTF-8 (opcional)
if (!$mysqli->set_charset("utf8")) {
    die("Erro ao definir o conjunto de caracteres para UTF-8: " . $mysqli->error);
}
?>