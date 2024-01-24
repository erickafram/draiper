<?php
session_start();
include('../verificar_sessao.php');

// Verificar se o fornecedor está logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'fornecedor') {
    header('Location: login.php');
    exit();
}

// Aqui você pode incluir qualquer lógica adicional necessária

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Fornecedor</title>
    <!-- Inclua aqui os links para os estilos do Bootstrap ou outros estilos, se necessário -->
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <h1>Dashboard do Fornecedor</h1>
    <p>Bem-vindo,    <?php echo $_SESSION['nome_completo']; ?>!</p>

    <!-- Botões para ações -->
    <div class="my-4">
        <a href="cadastro_produto.php" class="btn btn-primary">Cadastrar Novo Produto</a>
        <a href="listar_produtos.php" class="btn btn-info">Listar Meus Produtos</a>
    </div>

    <!-- Aqui você pode adicionar mais conteúdo ou funcionalidades ao dashboard -->
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
