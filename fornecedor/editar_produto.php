<?php
session_start();
include('../verificar_sessao.php');
include('../banco/db_connection.php');

$mensagem = "";
$erro = "";

// Verificar se um ID de produto foi fornecido
if (!isset($_GET['id'])) {
    echo "Nenhum produto especificado para edição.";
    exit;
}
$produto_id = $_GET['id'];

// Carregar dados do produto para edição
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT * FROM produtos WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows == 1) {
            $produto = $resultado->fetch_assoc();
        } else {
            $erro = "Produto não encontrado.";
        }
        $stmt->close();
    } else {
        $erro = "Erro na preparação da consulta: " . $mysqli->error;
    }
}

// Processamento do formulário quando é submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Aqui você processaria as mudanças no produto
    // Recuperação dos dados do formulário
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $estoque = $_POST['estoque'] ?? 0;
    $cores_disponiveis = $_POST['cores_disponiveis'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $composicao_material = $_POST['composicao_material'] ?? '';
    $peso = $_POST['peso'] ?? 0.0;
    $instrucoes_cuidado = $_POST['instrucoes_cuidado'] ?? '';
    $tempo_processamento_envio = $_POST['tempo_processamento_envio'] ?? '';
    $politica_devolucao = $_POST['politica_devolucao'] ?? '';
    $codigo_barras_sku = $_POST['codigo_barras_sku'] ?? '';
    $tags_palavras_chave = $_POST['tags_palavras_chave'] ?? '';
    $disponibilidade = $_POST['disponibilidade'] ?? '';
    $quantidade_minima_pedido = $_POST['quantidade_minima_pedido'] ?? 0;

    // Atualizar no banco de dados
    $sqlUpdate = "UPDATE produtos SET nome = ?, descricao = ?, categoria = ?, estoque = ?, cores_disponiveis = ?, marca = ?, composicao_material = ?, peso = ?, instrucoes_cuidado = ?, tempo_processamento_envio = ?, politica_devolucao = ?, codigo_barras_sku = ?, tags_palavras_chave = ?, disponibilidade = ?, quantidade_minima_pedido = ? WHERE id = ?";

    if ($stmtUpdate = $mysqli->prepare($sqlUpdate)) {
        $stmtUpdate->bind_param("sssisssssssssssi", $nome, $descricao, $categoria, $estoque, $cores_disponiveis, $marca, $composicao_material, $peso, $instrucoes_cuidado, $tempo_processamento_envio, $politica_devolucao, $codigo_barras_sku, $tags_palavras_chave, $disponibilidade, $quantidade_minima_pedido, $produto_id);

        if ($stmtUpdate->execute()) {
            $mensagem = "Produto atualizado com sucesso!";
            // Redirecione para a lista de produtos após atualizar com sucesso
            header("Location: listar_produtos.php");
            exit;
        } else {
            $erro = "Erro ao atualizar o produto: " . $stmtUpdate->error;
        }
        $stmtUpdate->close();
    } else {
        $erro = "Erro na preparação da consulta de atualização: " . $mysqli->error;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <!-- Incluir CSS e scripts necessários -->
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <?php
    if (!empty($mensagem)) {
        echo '<div class="alert alert-success">' . $mensagem . '</div>';
    }
    if (!empty($erro)) {
        echo '<div class="alert alert-danger">' . $erro . '</div>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $produto_id; ?>" method="post" enctype="multipart/form-data">
        <!-- Campos do Formulário -->
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $produto['nome']; ?>" required>
        </div>

        <!-- Repetir para outros campos, assegurando que eles estão pré-preenchidos com os dados do produto -->
        <!-- Exemplo: -->
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea class="form-control" id="descricao" name="descricao" required><?php echo $produto['descricao']; ?></textarea>
        </div>

        <!-- Adicione outros campos necessários -->

        <button type="submit" class="btn btn-primary">Atualizar Produto</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
