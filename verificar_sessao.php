<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifique se o usuário não está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Se não estiver logado, redirecione para a página de login ou outra página de destino
    header("Location: login.php"); // Substitua 'login.php' pela página de login apropriada
    exit();
}
?>