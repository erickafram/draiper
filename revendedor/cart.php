<?php
// Inclua os arquivos necessários
include('../verificar_sessao.php');
include('../banco/db_connection.php');
include('../includes/header.php');

// Função para calcular o total do carrinho
function calcularTotalCarrinho($carrinho)
{
    $total = 0;

    if (!empty($carrinho)) {
        foreach ($carrinho as $item) {
            $preco = floatval($item['preco']);
            $quantidade = intval($item['quantidade']);
            $total += $preco * $quantidade;
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

        foreach ($_SESSION['carrinho'] as $item) {
            $nomeProduto = $item['nome'];
            $precoUnitario = floatval($item['preco']);
            $quantidade = intval($item['quantidade']);
            $totalProduto = $precoUnitario * $quantidade;

            echo '<tr>';
            echo '<td>' . $nomeProduto . '</td>';
            echo '<td>' . $quantidade . '</td>';
            echo '<td>R$ ' . number_format($precoUnitario, 2, ',', '.') . '</td>';
            echo '<td>R$ ' . number_format($totalProduto, 2, ',', '.') . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';

        // Calcule e exiba o total do carrinho
        $totalCarrinho = calcularTotalCarrinho($_SESSION['carrinho']);
        echo '<p>Total do Carrinho: R$ ' . number_format($totalCarrinho, 2, ',', '.') . '</p>';

        // Adicione um botão de finalizar compra (você pode implementar esta funcionalidade)
        echo '<button class="btn btn-primary">Finalizar Compra</button>';
    }
    ?>
</div>
</body>
</html>
