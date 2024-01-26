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
    // Função para calcular o total de um item no carrinho
    function calcularTotalItem($precoUnitario, $quantidade)
    {
        return $precoUnitario * $quantidade;
    }

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
        echo '<th>Ações</th>'; // Adicione uma coluna para as ações (excluir)
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($_SESSION['carrinho'] as $key => $item) {
            $nomeProduto = $item['nome'];
            $precoUnitario = floatval($item['preco']);
            $quantidade = intval($item['quantidade']);
            $totalProduto = calcularTotalItem($precoUnitario, $quantidade);

            // Busque o caminho da imagem em destaque do banco de dados com base no nome do produto
            $nomeProduto = mysqli_real_escape_string($mysqli, $nomeProduto); // Evite SQL injection
            $sqlImagemDestaque = "SELECT imagem_destaque, quantidade_minima_pedido FROM produtos WHERE nome = '$nomeProduto'";
            $resultImagemDestaque = mysqli_query($mysqli, $sqlImagemDestaque);

            if ($resultImagemDestaque && mysqli_num_rows($resultImagemDestaque) > 0) {
                $imagemDestaqueRow = mysqli_fetch_assoc($resultImagemDestaque);
                $imagemDestaque = '../fornecedor/' . $imagemDestaqueRow['imagem_destaque'];
                $quantidadeMinimaPedido = intval($imagemDestaqueRow['quantidade_minima_pedido']);
            } else {
                $imagemDestaque = ''; // Defina um valor padrão caso a imagem não seja encontrada
                $quantidadeMinimaPedido = 1; // Defina uma quantidade mínima padrão
            }

            echo '<tr>';

            // Adicione uma coluna para a imagem ao lado da coluna do nome do produto
            echo '<td>';
            echo '<img src="' . $imagemDestaque . '" alt="' . $nomeProduto . '" width="80" height="84" style="margin-right:10px;">'; // Imagem
            echo $nomeProduto; // Nome do produto
            echo '</td>';

            // Verifique se a quantidade no carrinho é inferior à quantidade mínima de pedido
            if ($quantidade < $quantidadeMinimaPedido) {
                $quantidade = $quantidadeMinimaPedido; // Defina a quantidade mínima
                $_SESSION['carrinho'][$key]['quantidade'] = $quantidade; // Atualize a quantidade no carrinho
            }

            echo '<td>';
            // Adicione botões de "+" e "-" para escolher a quantidade
            echo '<div class="input-group">';
            echo '<button class="btn btn-outline-secondary btn-quantidade" data-key="' . $key . '" data-action="decrement">-</button>';
            echo '<input type="number" min="' . $quantidadeMinimaPedido . '" value="' . $quantidade . '" data-key="' . $key . '" class="form-control quantidade-input" readonly>';
            echo '<button class="btn btn-outline-secondary btn-quantidade" data-key="' . $key . '" data-action="increment">+</button>';
            echo '</div>';
            echo '</td>'; // Campo de entrada de quantidade
            echo '<td>R$ ' . number_format($precoUnitario, 2, ',', '.') . '</td>';
            echo '<td class="total-produto">R$ ' . number_format($totalProduto, 2, ',', '.') . '</td>';

            // Adicione um botão de exclusão para cada item no carrinho
            echo '<td><button class="btn btn-danger btn-excluir" data-key="' . $key . '">Excluir</button></td>';

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

<script>
    // Função para atualizar o total do produto e o carrinho quando a quantidade é alterada
    function atualizarTotalProduto($quantidadeInput, $totalProduto) {
        var quantidade = parseInt($quantidadeInput.val());
        var precoUnitario = parseFloat($quantidadeInput.closest('tr').find('td:eq(2)').text().replace('R$ ', '').replace(',', '.'));
        var total = quantidade * precoUnitario;
        $totalProduto.text('R$ ' + total.toFixed(2).replace('.', ','));
        atualizarTotalCarrinho();
    }

    // Função para atualizar o total do carrinho
    function atualizarTotalCarrinho() {
        var totalCarrinho = 0;
        $('.total-produto').each(function () {
            var totalProduto = parseFloat($(this).text().replace('R$ ', '').replace(',', '.'));
            totalCarrinho += totalProduto;
        });
        $('p:contains("Total do Carrinho")').text('Total do Carrinho: R$ ' + totalCarrinho.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    }

    // Quando a página é carregada
    $(document).ready(function () {
        // Atualizar o total do carrinho
        atualizarTotalCarrinho();

        // Quando o botão de "+" é clicado
        $('.btn-quantidade[data-action="increment"]').on('click', function () {
            var key = $(this).data('key');
            var $quantidadeInput = $('input[data-key="' + key + '"]');
            var quantidade = parseInt($quantidadeInput.val());
            var quantidadeMinimaPedido = parseInt($quantidadeInput.attr('min'));

            if (quantidade < quantidadeMinimaPedido) {
                quantidade = quantidadeMinimaPedido; // Impede que a quantidade seja menor que a quantidade mínima
            } else {
                quantidade++;
            }

            $quantidadeInput.val(quantidade);
            atualizarTotalProduto($quantidadeInput, $quantidadeInput.closest('tr').find('.total-produto'));
        });

        // Quando o botão de "-" é clicado
        $('.btn-quantidade[data-action="decrement"]').on('click', function () {
            var key = $(this).data('key');
            var $quantidadeInput = $('input[data-key="' + key + '"]');
            var quantidade = parseInt($quantidadeInput.val());
            var quantidadeMinimaPedido = parseInt($quantidadeInput.attr('min'));

            if (quantidade > quantidadeMinimaPedido) {
                quantidade--;
            }

            $quantidadeInput.val(quantidade);
            atualizarTotalProduto($quantidadeInput, $quantidadeInput.closest('tr').find('.total-produto'));
        });

        // Quando a quantidade é alterada
        $('.quantidade-input').on('input', function () {
            var key = $(this).data('key');
            var $quantidadeInput = $(this);
            var quantidade = parseInt($quantidadeInput.val());
            var quantidadeMinimaPedido = parseInt($quantidadeInput.attr('min'));

            if (quantidade < quantidadeMinimaPedido) {
                quantidade = quantidadeMinimaPedido; // Impede que a quantidade seja menor que a quantidade mínima
            }

            $quantidadeInput.val(quantidade);
            atualizarTotalProduto($quantidadeInput, $quantidadeInput.closest('tr').find('.total-produto'));
        });

        // Quando o botão de exclusão é clicado
        $('.btn-excluir').on('click', function () {
            var key = $(this).data('key');
            // Use o PHP para obter a contagem atual de itens no carrinho
            // Atualize a sessão do carrinho (você pode usar uma solicitação AJAX para fazer isso)
            $.post('excluir_item_carrinho.php', { item_id: key }, function (data) {
                if (data.success) {
                    window.location.reload(); // Isso recarrega a página, você pode implementar a atualização da tabela usando AJAX
                } else {
                    // Lide com erros ou mensagens de falha, se necessário
                    console.log('Erro ao excluir item: ' + data.message);
                }
            }, 'json');
        });
    });
</script>

</body>
</html>
