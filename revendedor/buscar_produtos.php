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
            echo '<div class="col-md-3">';
            echo '<div class="card mb-4" style="max-width: 188px;">'; // Defina a largura máxima do card
            echo '<div style="position: relative; max-width: 188px; max-height: 318px; overflow: hidden;">'; // Container para a imagem e texto com largura e altura máximas
            echo '<img class="card-img-top" src="' . $caminhoImagens . $row['imagem_destaque'] . '" alt="' . $row['nome'] . '">';
            echo '<div class="text-center" style="position: absolute; bottom: 0; left: 0; right: 0; background-color: rgb(57 222 123); color: white; font-size: 0.75rem;">Entrega Imediata</div>';
            echo '</div>'; // Fechar o container da imagem e do texto
            echo '<div class="card-body">';
            echo '<h5 class="card-title" style="line-height: 14px; font-size: .75rem !important;">' . $row['nome'] . '</h5>';
            echo '<p class="card-text" style="color: #ee4d2d; font-size: 0.85rem;">R$ ' . number_format($row['preco'], 2, ',', '.') . '</p>';
            echo '<p class="card-text" style="font-size:12px;">Estoque: ' . $row['estoque'] . ' unidades</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
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
