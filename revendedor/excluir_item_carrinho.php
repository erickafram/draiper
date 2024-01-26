<?php
session_start();

if (isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);

    // Verifique se o item existe no carrinho
    if (isset($_SESSION['carrinho'][$item_id])) {
        // Remova o item do carrinho
        unset($_SESSION['carrinho'][$item_id]);

        // Atualize a contagem de itens no carrinho (opcional)
        $total_itens_carrinho = count($_SESSION['carrinho']);

        // Você pode retornar um JSON para indicar o sucesso da exclusão
        $response = array(
            'success' => true,
            'message' => 'Item excluído com sucesso',
            'total_itens_carrinho' => $total_itens_carrinho
        );

        echo json_encode($response);
    } else {
        // Item não encontrado no carrinho
        $response = array(
            'success' => false,
            'message' => 'Item não encontrado no carrinho'
        );

        echo json_encode($response);
    }
} else {
    // Nenhum ID de item foi enviado
    $response = array(
        'success' => false,
        'message' => 'ID de item não fornecido'
    );

    echo json_encode($response);
}
?>
