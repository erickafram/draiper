<?php
// Inicie a sessão
session_start();

// Destrua todas as variáveis de sessão
session_unset();

// Destrua a sessão
session_destroy();

// Redirecione para a página de login ou outra página de destino após o logout
header("Location: login.php"); // Substitua 'login.php' pela página de login apropriada
exit();
?>
