<?php
// Inclua os arquivos necessários
include('../verificar_sessao.php');
include('../banco/db_connection.php');

// Verifique se os parâmetros necessários foram recebidos via POST
if (isset($_POST['produto_id']) && isset($_POST['quantidade']) && isset($_POST['tamanho']) && isset($_POST['cor'])) {
    $produto_id = $_POST['produto_id'];
    $quantidadeAdicionada = $_POST['quantidade'];
    $tamanho = $_POST['tamanho'];
    $cor = $_POST['cor'];

    // Consulta SQL para obter o produto com base no ID
    $sqlProduto = "SELECT * FROM produtos WHERE id = " . intval($produto_id);
    $resultProduto = mysqli_query($mysqli, $sqlProduto);

    if ($resultProduto && mysqli_num_rows($resultProduto) > 0) {
        $produto = mysqli_fetch_assoc($resultProduto);

        // Inicialize a sessão do carrinho, se ainda não foi feito
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = array();
        }

        // Verifique se o produto já está no carrinho
        $produtoEncontradoNoCarrinho = false;
        foreach ($_SESSION['carrinho'] as $key => $item) {
            if ($item['produto_id'] == $produto_id && $item['tamanho'] == $tamanho && $item['cor'] == $cor) {
                // Produto já está no carrinho, atualize a quantidade
                $_SESSION['carrinho'][$key]['quantidade'] += $quantidadeAdicionada;
                $produtoEncontradoNoCarrinho = true;
                break;
            }
        }

        if (!$produtoEncontradoNoCarrinho) {
            // Produto não está no carrinho, adicione como novo item
            $_SESSION['carrinho'][] = array(
                'produto_id' => $produto_id,
                'nome' => $produto['nome'],
                'preco' => $produto['preco'],
                'quantidade' => $quantidadeAdicionada,
                'tamanho' => $tamanho,
                'cor' => $cor
            );
        }

        // Retorne uma resposta de sucesso
        echo json_encode(array('status' => 'success', 'message' => 'Produto adicionado/atualizado no carrinho com sucesso!'));
    } else {
        // Produto não encontrado
        echo json_encode(array('status' => 'error', 'message' => 'Produto não encontrado.'));
    }
} else {
    // Parâmetros ausentes
    echo json_encode(array('status' => 'error', 'message' => 'Parâmetros ausentes.'));
}
?>
