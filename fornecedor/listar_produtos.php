<?php
session_start();
include('../verificar_sessao.php');
include('../banco/db_connection.php');

if (!isset($_SESSION['usuario_id'])) {
    // Redirecionar para a página de login se o fornecedor não estiver logado
    header('Location: login.php');
    exit();
}

$fornecedor_id = $_SESSION['usuario_id'];
$produtos = array();

// Consulta ao banco de dados para buscar produtos do fornecedor
$sql = "SELECT * FROM produtos WHERE fornecedor_id = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $fornecedor_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($linha = $resultado->fetch_assoc()) {
        $produtos[] = $linha;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <!-- Outros elementos do head -->
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <h1>Meus Produtos</h1>

    <table class="table">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Categoria</th>
            <th>Preço</th>
            <th>Imagens</th>
            <th>Cores Disponíveis</th>
            <th>Ações</th> <!-- Coluna adicional para botões de ação -->
        </tr>
        </thead>
        <tbody>
        <?php foreach ($produtos as $produto): ?>
            <tr>
                <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                <td><?php echo htmlspecialchars($produto['descricao']); ?></td>
                <td><?php echo htmlspecialchars($produto['categoria']); ?></td>
                <td>R$ <?php echo htmlspecialchars(number_format($produto['preco'], 2, ',', '.')); ?></td>
                <td>
                    <?php
                    $imagens = explode(',', $produto['imagens']);
                    foreach ($imagens as $imagem) {
                        echo "<img src='$imagem' style='width: 50px; height: 50px;'>";
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($produto['cores_disponiveis']); ?></td>
                <td>
                    <!-- Botão de editar -->
                    <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-primary">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
