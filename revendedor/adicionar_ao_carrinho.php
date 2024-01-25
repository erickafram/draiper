<?php
// Inclua os arquivos necessários
include('../verificar_sessao.php');
include('../banco/db_connection.php');

// Verifique se os parâmetros necessários foram recebidos via POST
if (isset($_POST['produto_id']) && isset($_POST['quantidade']) && isset($_POST['tamanho']) && isset($_POST['cor'])) {
    $produto_id = $_POST['produto_id'];
    $quantidade = $_POST['quantidade'];
    $tamanho = $_POST['tamanho'];
    $cor = $_POST['cor'];

    // Consulta SQL para obter o produto com base no ID
    $sqlProduto = "SELECT * FROM produtos WHERE id = " . intval($produto_id);
    $resultProduto = mysqli_query($mysqli, $sqlProduto);

    if ($resultProduto && mysqli_num_rows($resultProduto) > 0) {
        $produto = mysqli_fetch_assoc($resultProduto);

        // Verifique se a quantidade solicitada está disponível em estoque
        if ($quantidade > 0 && $quantidade <= $produto['estoque']) {
            // Crie um array para representar o produto a ser adicionado ao carrinho
            $produto_a_adicionar = array(
                'produto_id' => $produto_id,
                'nome' => $produto['nome'],
                'preco' => $produto['preco'],
                'quantidade' => $quantidade,
                'tamanho' => $tamanho,
                'cor' => $cor
            );

            // Verifique se a variável de sessão 'carrinho' existe, se não, inicialize-a como um array vazio
            if (!isset($_SESSION['carrinho'])) {
                $_SESSION['carrinho'] = array();
            }

            // Adicione o produto ao carrinho
            $_SESSION['carrinho'][] = $produto_a_adicionar;

            // Retorne uma resposta de sucesso como JSON
            echo json_encode(array('status' => 'success', 'message' => 'Produto adicionado ao carrinho com sucesso!'));
            exit();
        } else {
            // A quantidade solicitada não está disponível em estoque
            echo json_encode(array('status' => 'error', 'message' => 'Quantidade indisponível em estoque.'));
            exit();
        }
    } else {
        // Produto não encontrado
        echo json_encode(array('status' => 'error', 'message' => 'Produto não encontrado.'));
        exit();
    }
} else {
    // Parâmetros ausentes
    echo json_encode(array('status' => 'error', 'message' => 'Parâmetros ausentes.'));
    exit();
}
?>
