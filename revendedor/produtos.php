<?php
// Inclua os arquivos necessários
include('../verificar_sessao.php');
include('../banco/db_connection.php');
include('../includes/header.php');

// Caminho base para as imagens dos produtos
$caminhoImagens = '../fornecedor/';

// Consulta SQL para listar as categorias que têm produtos associados
$sqlCategorias = "SELECT DISTINCT c.id, c.nome
                 FROM categorias c
                 JOIN produtos p ON c.id = p.categoria";

$resultCategorias = mysqli_query($mysqli, $sqlCategorias);

if (!$resultCategorias) {
    die("Erro ao consultar as categorias: " . mysqli_error($mysqli));
}

// Verificar se uma categoria foi selecionada (por meio de um parâmetro de consulta na URL)
$selectedCategory = isset($_GET['categoria']) ? $_GET['categoria'] : null;

// Consulta SQL para listar os produtos da categoria selecionada (ou todos os produtos, se nenhuma categoria for selecionada)
$sqlProdutos = "SELECT * FROM produtos";

if (!is_null($selectedCategory)) {
    $sqlProdutos .= " WHERE categoria = " . intval($selectedCategory);
}

$resultProdutos = mysqli_query($mysqli, $sqlProdutos);

if (!$resultProdutos) {
    die("Erro ao consultar os produtos: " . mysqli_error($mysqli));
}

// Ícones das categorias
$iconesCategorias = [
    34 => 'fas fa-tshirt',     // Blusas
    35 => 'fas fa-pants',      // Calças (ou 'fas fa-trousers')
    36 => 'fas fa-tshirt',     // Camisetas (pode ser o mesmo ícone das Blusas)
    37 => 'fas fa-dress',      // Vestidos
    38 => 'fas fa-skirt',      // Saias
    39 => 'fas fa-jacket',     // Jaquetas
    40 => 'fas fa-shorts',     // Shorts
    41 => 'fas fa-shorts',     // Bermudas (pode ser o mesmo ícone dos Shorts)
    42 => 'fas fa-underwear',  // Lingerie
    43 => 'fas fa-bed',        // Pijamas
    44 => 'fas fa-suitcase',   // Macacão
    45 => 'fas fa-blazer',     // Blazer
    46 => 'fas fa-tshirt',     // Crop Top (pode ser o mesmo ícone das Blusas)
    47 => 'fas fa-tshirt',     // Body (pode ser o mesmo ícone das Blusas)
    48 => 'fas fa-belt',       // Cinto
    49 => 'fas fa-shoe-prints',// Calçados
    50 => 'fas fa-sunglasses', // Acessórios
    51 => 'fas fa-socks',      // Meias
    52 => 'fas fa-hoodie'      // Moletons
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Produtos</title>
</head>
<body>
<!-- Seção de Categorias -->
<div class="categorias">
    <div class="container">
        <div class="categorias-icons">
            <?php
            while ($categoria = mysqli_fetch_assoc($resultCategorias)) {
                $isActive = ($selectedCategory == $categoria['id']) ? 'active' : '';
                echo '<a class="categoria-icon ' . $isActive . '" href="?categoria=' . $categoria['id'] . '">';
                echo '<i class="' . $iconesCategorias[$categoria['id']] . '"></i>';
                echo '<p class="categoria-text">' . $categoria['nome'] . '</p>';
                echo '</a>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Campo de pesquisa -->
<div class="container search-container">
    <div class="form-group">
        <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar Produto">
    </div>
</div>

<!-- Resultados da pesquisa -->
<div class="container" id="searchResults">
</div>

<div class="container" id="productList">

    <div class="row">
        <?php
        $count = 0;
        while ($row = mysqli_fetch_assoc($resultProdutos)) {
            if ($count % 4 == 0) {
                echo '</div><div class="row">';
            }

            echo '<div class="col-md-3">';
            echo '<div class="card mb-4" style="max-width: 188px;">';
            echo '<a href="cart.php?add_to_cart=1&produto_id=' . $row['id'] . '">'; // Adicione o link para adicionar ao carrinho
            echo '<div style="position: relative; max-width: 188px; max-height: 318px; overflow: hidden;">';
            echo '<img class="card-img-top" src="' . $caminhoImagens . $row['imagem_destaque'] . '" alt="' . $row['nome'] . '">';
            echo '<div class="text-center" style="position: absolute; bottom: 0; left: 0; right: 0; background-color: rgb(57 222 123); color: white; font-size: 0.75rem;">Entrega Imediata</div>';
            echo '</div>';
            echo '<div class="card-body">';
            echo '<h5 class="card-title" style="line-height: 14px; font-size: .75rem !important;">' . $row['nome'] . '</h5>';
            echo '<p class="card-text" style="color: #ee4d2d; font-size: 0.85rem;">R$ ' . number_format($row['preco'], 2, ',', '.') . '</p>';
            echo '<p class="card-text" style="font-size:12px;">Estoque: ' . $row['estoque'] . ' unidades</p>';
            echo '</div>';
            echo '</a>'; // Feche o link
            echo '</div>';
            echo '</div>';

            $count++;
        }
        ?>

    </div>
</div>
</body>
</html>

<!-- JavaScript para a pesquisa em tempo real -->
<script>
    $(document).ready(function() {
        // Selecionar o campo de pesquisa
        var searchInput = $('#searchInput');

        // Selecionar a área de resultados da pesquisa
        var searchResults = $('#searchResults');

        // Selecionar a lista de produtos
        var productList = $('#productList');

        // Função para atualizar os resultados da pesquisa com base no texto digitado
        function updateSearchResults() {
            var searchTerm = searchInput.val().trim();

            // Se o termo de pesquisa estiver vazio, mostrar a lista de produtos
            if (searchTerm === '') {
                productList.removeClass('hide');
                searchResults.empty();
            } else {
                // Enviar a solicitação AJAX para buscar produtos com base no termo de pesquisa
                $.ajax({
                    type: 'GET',
                    url: 'buscar_produtos.php', // Substitua pelo URL correto do arquivo PHP para buscar produtos
                    data: { termo: searchTerm }, // Enviar o termo de pesquisa para o servidor
                    dataType: 'html',
                    success: function(response) {
                        // Ocultar a lista de produtos e exibir os resultados da pesquisa
                        productList.addClass('hide');
                        searchResults.html(response); // Exibir os resultados da pesquisa
                    }
                });
            }
        }

        // Adicionar um evento de digitação para o campo de pesquisa
        searchInput.on('input', updateSearchResults);
    });
</script>
