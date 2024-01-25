<?php
// Inclua os arquivos necessários
include('../verificar_sessao.php');
include('../banco/db_connection.php');
include('../includes/header.php');

$caminhoImagens = '../fornecedor/';

// Verifique se o parâmetro 'produto_id' está definido na URL
if (isset($_GET['produto_id'])) {
    $produto_id = $_GET['produto_id'];

    // Consulta SQL para obter os detalhes do produto
    $sqlProduto = "SELECT * FROM produtos WHERE id = " . intval($produto_id);
    $resultProduto = mysqli_query($mysqli, $sqlProduto);

    if ($resultProduto && mysqli_num_rows($resultProduto) > 0) {
        $produto = mysqli_fetch_assoc($resultProduto);
        $imagens = explode(',', $produto['imagens']); // Divida a lista de imagens em um array
        $coresDisponiveis = explode(',', $produto['cores_disponiveis']); // Divida a lista de cores em um array
        $tamanhosDisponiveis = explode(',', $produto['tamanho']); // Divida a lista de tamanhos em um array

        // Consulta SQL para obter produtos relacionados (você precisa definir a lógica para isso)
        $sqlProdutosRelacionados = "SELECT * FROM produtos WHERE categoria = " . intval($produto['categoria']) . " AND id <> " . intval($produto_id) . " LIMIT 4";
        $resultProdutosRelacionados = mysqli_query($mysqli, $sqlProdutosRelacionados);

        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <title>Detalhes do Produto</title>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        </head>
        <body>
        <div class="container">
            <h1>Detalhes do Produto</h1>
            <div class="row">
                <div class="col-md-6">
                    <!-- Exiba a imagem em destaque com um tamanho máximo de 100% de largura e altura automática -->
                    <img id="main-product-image" src="<?php echo $caminhoImagens . $produto['imagem_destaque']; ?>"
                         alt="<?php echo $produto['nome']; ?>"
                         style="max-width: 100%; height: auto;">
                </div>
                <div class="col-md-6">
                    <h2><?php echo $produto['nome']; ?></h2>
                    <p>Preço: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    <p>Estoque: <?php echo $produto['estoque']; ?> unidades</p>
                    <form action="cart.php" method="get">
                        <input type="hidden" name="add_to_cart" value="1">
                        <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">

                        <!-- Opções de tamanho -->
                        <label for="tamanho">Escolha o tamanho:</label>
                        <select name="tamanho" id="tamanho">
                            <?php foreach ($tamanhosDisponiveis as $tamanho) { ?>
                                <option value="<?php echo $tamanho; ?>"><?php echo $tamanho; ?></option>
                            <?php } ?>
                        </select>

                        <!-- Opções de cor -->
                        <label for="cor">Escolha a cor:</label>
                        <select name="cor" id="cor">
                            <?php foreach ($coresDisponiveis as $cor) { ?>
                                <option value="<?php echo $cor; ?>"><?php echo $cor; ?></option>
                            <?php } ?>
                        </select>

                        <!-- Quantidade mínima de compra -->
                        <input type="hidden" name="quantidade_minima" value="<?php echo $produto['quantidade_minima_pedido']; ?>">

                        <label for="quantidade">Quantidade:</label>
                        <input type="number" name="quantidade" id="quantidade" value="1" min="<?php echo $produto['quantidade_minima_pedido'] ?: 1; ?>"
                               max="<?php echo $produto['estoque']; ?>">
                        <?php if ($produto['quantidade_minima_pedido'] > 1) { ?>
                            <p>Quantidade mínima de compra: <?php echo $produto['quantidade_minima_pedido']; ?></p>
                        <?php } ?>
                        <button type="button" class="btn btn-primary" id="addToCart">Adicionar ao Carrinho</button>
                        <button type="button" class="btn btn-success" id="finalizePurchase">Finalizar Compra</button>
                    </form>
                </div>
            </div>

            <!-- Exiba as imagens adicionais vinculadas ao produto -->
            <div class="row">
                <div class="col-md-12">
                    <?php foreach ($imagens as $imagem) { ?>
                        <img src="<?php echo $caminhoImagens . $imagem; ?>" alt="<?php echo $produto['nome']; ?>"
                             style="max-width: 100px; max-height: 100px; width: auto; height: auto; margin: 5px;"
                             class="additional-image" onclick="trocarImagem('<?php echo $caminhoImagens . $imagem; ?>')">
                    <?php } ?>
                </div>
            </div>
            <!-- Exiba a descrição do produto -->
            <div class="row">
                <div class="col-md-12">
                    <h2>Descrição do Produto</h2>
                    <p><?php echo $produto['descricao']; ?></p>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                // Quando o botão "Adicionar ao Carrinho" for clicado
                $('#addToCart').on('click', function () {
                    var quantidade = $('#quantidade').val();
                    var produtoId = <?php echo $produto['id']; ?>;
                    var tamanho = $('#tamanho').val();
                    var cor = $('#cor').val();
                    var quantidadeMinima = <?php echo $produto['quantidade_minima_pedido']; ?>;

                    // Verifica se a quantidade selecionada é maior ou igual à quantidade mínima
                    if (quantidade < quantidadeMinima) {
                        alert('A quantidade mínima de compra é ' + quantidadeMinima + '.');
                        return; // Impede o envio da solicitação AJAX
                    }

                    // Enviar uma solicitação AJAX para adicionar o produto ao carrinho
                    $.ajax({
                        url: 'adicionar_ao_carrinho.php',
                        type: 'POST',
                        data: {
                            produto_id: produtoId,
                            quantidade: quantidade,
                            tamanho: tamanho,
                            cor: cor
                        },
                        success: function (response) {
                            // Exiba uma mensagem de sucesso ou atualize o carrinho na interface do usuário
                            alert('Produto adicionado ao carrinho com sucesso!');
                        },
                        error: function () {
                            // Lidar com erros, se houver algum
                            alert('Erro ao adicionar o produto ao carrinho.');
                        }
                    });
                });

                // Quando o botão "Finalizar Compra" for clicado
                $('#finalizePurchase').on('click', function () {
                    window.location.href = 'cart.php'; // Redirecione para a página de carrinho
                });
            });

            // Função para trocar a imagem em destaque
            function trocarImagem(imagem) {
                $('#main-product-image').attr('src', imagem);
            }
        </script>
        </body>
        </html>
        <?php
    } else {
        // Produto não encontrado, você pode adicionar uma mensagem de erro ou redirecionar para uma página de erro
        echo "Produto não encontrado.";
    }
} else {
    // Se 'produto_id' não estiver definido na URL, redirecione para a página de produtos
    header("Location: produtos.php");
    exit();
}
?>
