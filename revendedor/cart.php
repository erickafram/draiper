<?php
// Inclua os arquivos necessários
include('../verificar_sessao.php');
include('../banco/db_connection.php');
include('../includes/header.php');

// Verifique se a variável 'add_to_cart' está definida na URL para adicionar um produto ao carrinho
if (isset($_GET['add_to_cart']) && isset($_GET['produto_id'])) {
    $produto_id = $_GET['produto_id'];

    // Verifique se o produto já existe no carrinho
    if (!isset($_SESSION['carrinho'][$produto_id])) {
        // Se o produto não existir no carrinho, adicione-o
        $_SESSION['carrinho'][$produto_id] = 1;
    } else {
        // Se o produto já existir no carrinho, aumente a quantidade
        $_SESSION['carrinho'][$produto_id]++;
    }
}

// Função para calcular o total do carrinho
function calcularTotalCarrinho($mysqli)
{
    $total = 0;

    if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
            // Consulta SQL para obter o preço do produto
            $sqlProduto = "SELECT preco FROM produtos WHERE id = " . intval($produto_id);
            $resultProduto = mysqli_query($mysqli, $sqlProduto);

            if ($resultProduto && mysqli_num_rows($resultProduto) > 0) {
                $row = mysqli_fetch_assoc($resultProduto);
                $preco = $row['preco'];
                $total += $preco * $quantidade;
            }
        }
    }

    return $total;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Carrinho de Compras</title>
</head>
<body>
<!-- Seção de Carrinho -->
<div class="container">
    <h1>Carrinho de Compras</h1>
    <?php
    // Verifique se o carrinho está vazio
    if (empty($_SESSION['carrinho'])) {
        echo '<p>Seu carrinho está vazio.</p>';
    } else {
        echo '<table class="table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Produto</th>';
        echo '<th>Quantidade</th>';
        echo '<th>Preço Unitário</th>';
        echo '<th>Total</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
            // Consulta SQL para obter os detalhes do produto
            $sqlProduto = "SELECT nome, preco FROM produtos WHERE id = " . intval($produto_id);
            $resultProduto = mysqli_query($mysqli, $sqlProduto);

            if ($resultProduto && mysqli_num_rows($resultProduto) > 0) {
                $row = mysqli_fetch_assoc($resultProduto);
                $nomeProduto = $row['nome'];
                $precoUnitario = $row['preco'];
                $totalProduto = $precoUnitario * $quantidade;

                echo '<tr>';
                echo '<td>' . $nomeProduto . '</td>';
                echo '<td>' . $quantidade . '</td>';
                echo '<td>R$ ' . number_format($precoUnitario, 2, ',', '.') . '</td>';
                echo '<td>R$ ' . number_format($totalProduto, 2, ',', '.') . '</td>';
                echo '</tr>';
            }
        }

        echo '</tbody>';
        echo '</table>';

        // Calcule e exiba o total do carrinho
        $totalCarrinho = calcularTotalCarrinho($mysqli);
        echo '<p>Total do Carrinho: R$ ' . number_format($totalCarrinho, 2, ',', '.') . '</p>';

        // Adicione um botão de finalizar compra (você pode implementar esta funcionalidade)
        echo '<button class="btn btn-primary">Finalizar Compra</button>';
    }
    ?>
</div>
</body>
</html>
