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
        <div class="container" style="padding-top:20px;">
            <!-- Breadcrumb -->
            <nav aria-label="Breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Página Inicial</a></li>
                    <li class="breadcrumb-item"><a href="produtos.php">Produtos</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $produto['nome']; ?></li>
                </ol>
            </nav>
            <div class="row">
                <div class="col-md-6">
                    <!-- Imagem em destaque -->
                    <img id="main-product-image" src="<?php echo $caminhoImagens . $produto['imagem_destaque']; ?>"
                         alt="<?php echo $produto['nome']; ?>"
                         style="max-width: 100%; height: auto;">

                    <!-- Imagens adicionais -->
                    <div class="additional-images">
                        <?php foreach ($imagens as $imagem) { ?>
                            <img src="<?php echo $caminhoImagens . $imagem; ?>" alt="<?php echo $produto['nome']; ?>"
                                 style="max-width: 100px; max-height: 100px; width: auto; height: auto; margin: 5px;"
                                 class="additional-image" onclick="trocarImagem('<?php echo $caminhoImagens . $imagem; ?>')">
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class style=" bottom: 0; left: 0; right: 0; font-size: 0.75rem;">
                        <i class="far fa-clock"></i> Entrega Rápida
                    </div>
                    <h2><?php echo $produto['nome']; ?></h2>
                    <p>Preço: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    <p>Estoque: <?php echo $produto['estoque']; ?> unidades</p>
                    <form action="cart.php" method="get">
                        <input type="hidden" name="add_to_cart" value="1">
                        <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">

                        <!-- Opções de tamanho -->
                        <div class="form-group">
                            <label for="tamanho">Escolha o tamanho:</label>
                            <select name="tamanho" id="tamanho" class="form-control">
                                <?php foreach ($tamanhosDisponiveis as $tamanho) { ?>
                                    <option value="<?php echo $tamanho; ?>"><?php echo $tamanho; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Opções de cor -->
                        <div class="form-group">
                            <label for="cor">Escolha a cor:</label>
                            <select name="cor" id="cor" class="form-control">
                                <?php foreach ($coresDisponiveis as $cor) { ?>
                                    <option value="<?php echo $cor; ?>"><?php echo $cor; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Quantidade mínima de compra -->
                        <div class="form-group">
                            <label for="quantidade">Quantidade:</label>
                            <input type="number" name="quantidade" id="quantidade" value="1" min="<?php echo $produto['quantidade_minima_pedido'] ?: 1; ?>"
                                   max="<?php echo $produto['estoque']; ?>" class="form-control">
                            <?php if ($produto['quantidade_minima_pedido'] > 1) { ?>
                                <p class="quantidade-minima">Quantidade mínima de compra: <?php echo $produto['quantidade_minima_pedido']; ?></p>
                            <?php } ?>
                        </div>

                        <button type="button" class="btn btn-primary" id="addToCart">Adicionar ao Carrinho</button>
                        <button type="button" class="btn btn-success" id="finalizePurchase">Finalizar Compra</button>
                    </form>
                </div>
            </div>

            <!-- Exiba a descrição do produto dentro de um container organizado -->
            <div class="container">
                <div class="row">
                    <div class="col-md-12" style="margin-top:40px;margin-top:20px;">
                        <h4>Descrição do Produto</h4>
                        <p><?php echo $produto['descricao']; ?></p>
                    </div>
                </div>
            </div>


            <script>
                $(document).ready(function () {
                    $('#addToCart').on('click', function () {
                        var quantidade = $('#quantidade').val();
                        var produtoId = <?php echo $produto['id']; ?>;
                        var tamanho = $('#tamanho').val();
                        var cor = $('#cor').val();

                        // Verifica se a quantidade selecionada é maior ou igual à quantidade mínima
                        if (quantidade < <?php echo $produto['quantidade_minima_pedido']; ?>) {
                            alert('A quantidade mínima de compra é ' + <?php echo $produto['quantidade_minima_pedido']; ?> + '.');
                            return;
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
                                alert('Produto adicionado ao carrinho com sucesso!');
                                // Chama a função para atualizar a quantidade no carrinho
                                atualizarQuantidadeCarrinho();
                            },
                            error: function () {
                                alert('Erro ao adicionar o produto ao carrinho.');
                            }
                        });
                    });

                    function atualizarQuantidadeCarrinho() {
                        // Atualiza a quantidade no carrinho no header
                        $.ajax({
                            url: 'obter_quantidade_carrinho.php',
                            type: 'GET',
                            success: function (response) {
                                var data = JSON.parse(response);
                                $('#carrinho-quantidade').text(data.quantidade);
                            },
                            error: function () {
                                console.log('Erro ao obter a quantidade do carrinho.');
                            }
                        });
                    }

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
