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
            <script>
                $(document).ready(function () {
                    // Quando uma imagem adicional for clicada, substitua a imagem em destaque
                    $('.additional-image').on('click', function () {
                        var imageUrl = $(this).attr('src');
                        $('#main-product-image').attr('src', imageUrl);
                    });
                });
            </script>
        </head>
        <body>
        <div class="container">
            <h1>Detalhes do Produto</h1>
            <div class="row">
                <div class="col-md-6">
                    <!-- Exiba a imagem em destaque com um tamanho máximo de 450x450 pixels -->
                    <img id="main-product-image" src="<?php echo $caminhoImagens . $produto['imagem_destaque']; ?>"
                         alt="<?php echo $produto['nome']; ?>"
                         style="max-width: 450px; max-height: 450px; width: auto; height: auto;">
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

                        <label for="quantidade">Quantidade:</label>
                        <input type="number" name="quantidade" id="quantidade" value="1" min="<?php echo $produto['quantidade_minima_pedido'] ?: 1; ?>"
                               max="<?php echo $produto['estoque']; ?>">
                        <?php if ($produto['quantidade_minima_pedido'] > 1) { ?>
                            <p>Quantidade mínima de compra: <?php echo $produto['quantidade_minima_pedido']; ?></p>
                        <?php } ?>
                        <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
                    </form>

                </div>
            </div>

            <!-- Exiba as imagens adicionais vinculadas ao produto -->
            <div class="row">
                <?php foreach ($imagens as $imagem) { ?>
                    <div class="col-md-3">
                        <img src="<?php echo $caminhoImagens . $imagem; ?>" alt="<?php echo $produto['nome']; ?>"
                             style="max-width: 150px; max-height: 150px; width: auto; height: auto;"
                             class="additional-image">
                    </div>
                <?php } ?>
            </div>
            <!-- Exiba a descrição do produto -->
            <div class="row">
                <div class="col-md-12">
                    <h2>Descrição do Produto</h2>
                    <p><?php echo $produto['descricao']; ?></p>
                </div>
            </div>
        </div>
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
