<?php
// Inclua o arquivo de verificação de sessão para garantir que o usuário esteja logado
include('../verificar_sessao.php');

// Verifique se o usuário possui o nível de acesso de revendedor
if ($_SESSION['nivel_acesso'] !== 'revendedor') {
    // Se o usuário não for um revendedor, redirecione-o para a página apropriada
    header("Location: ../index.php"); // Redirecione para a página de login ou outra página de destino
    exit();
}

// Inclua o cabeçalho da página
include('../includes/header.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Revendedor</title>
    <!-- Adicione aqui os links para o CSS, se necessário -->
</head>
<body>
<div class="container mt-5">
    <h1>Dashboard do Revendedor</h1>
    <p>Bem-vindo, <?php echo $_SESSION['nome_completo']; ?>!</p>

    <!-- Adicione o conteúdo do dashboard aqui -->

    <a href="../logout.php" class="btn btn-primary">Sair</a>
</div>

<!-- Adicione aqui os links para os scripts JavaScript, se necessário -->
</body>
</html>
