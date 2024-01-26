<?php
// Inclua os arquivos necessários e conecte-se ao banco de dados
include('../verificar_sessao.php');
include('../banco/db_connection.php');

// Caminho base para as imagens dos produtos
$caminhoImagens = '../fornecedor/'; // Defina o caminho correto para as imagens

// Verificar se o termo de pesquisa foi enviado via GET
if (isset($_GET['termo'])) {
    $termo = mysqli_real_escape_string($mysqli, $_GET['termo']);

    // Consulta SQL para buscar produtos com base no termo de pesquisa
    $sql = "SELECT * FROM produtos WHERE nome LIKE '%$termo%'";
    $result = mysqli_query($mysqli, $sql);

    // Verificar se houve resultados
    if ($result && mysqli_num_rows($result) > 0) {
        echo '<div class="row">'; // Iniciar a linha Bootstrap

        // Loop para exibir os resultados
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="col-md-3 col-sm-6 mb-4">';
            echo '<div class="card" style="width: 100%;">'; // Ajuste na largura do card
            echo '<a href="detalhes_produto.php?produto_id=' . $row['id'] . '">'; // Altere o link para redirecionar para detalhes_produto.php
            echo '<div style="position: relative; overflow: hidden;">';
            echo '<img class="card-img-top" src="' . $caminhoImagens . $row['imagem_destaque'] . '" alt="' . $row['nome'] . '">';
            echo '<div class="text-center" style="position: absolute; bottom: 0; left: 0; right: 0; background-color: #d0011b; color: white; font-size: 0.75rem;">Entrega Imediata</div>';
            echo '</div>';
            echo '<div class="card-body">';
            echo '<h5 class="card-title" style="line-height: 14px; font-size: .75rem !important;">' . $row['nome'] . '</h5>';
            echo '<p class="card-text" style="color: #d0011b; font-size: 0.85rem;">R$ ' . number_format($row['preco'], 2, ',', '.') . '</p>';
            echo '<p class="card-text" style="font-size:12px;">Estoque: ' . $row['estoque'] . ' unidades</p>';
            echo '</div>';
            echo '</a>'; // Feche o link
            echo '</div>'; // Feche o div do card
            echo '</div>'; // Feche o div da coluna
        }

        echo '</div>'; // Fechar a linha Bootstrap
    } else {
        // Nenhum resultado encontrado
        echo '<p>Nenhum produto encontrado.</p>';
    }
} else {
    // Nenhum termo de pesquisa enviado
    echo '<p>Por favor, digite um termo de pesquisa.</p>';
}
?>
