<?php
include('../verificar_sessao.php');

$totalItensCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $totalItensCarrinho += intval($item['quantidade']);
    }
}

echo json_encode(array('quantidade' => $totalItensCarrinho));
?>
