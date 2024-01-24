<?php
// Inicia a sessão e inclui a conexão com o banco de dados
session_start();
include('../verificar_sessao.php');
include('../banco/db_connection.php');

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomeCategoria = $_POST['nome_categoria'] ?? '';

    if (!empty($nomeCategoria)) {
        // Prepara a consulta para inserir a categoria
        $sql = "INSERT INTO categorias (nome) VALUES (?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $nomeCategoria);

            if ($stmt->execute()) {
                $mensagem = "Categoria cadastrada com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar a categoria: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensagem = "Erro na preparação da consulta: " . $mysqli->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Categoria</title>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <h2>Cadastro de Categoria</h2>

    <?php if (!empty($mensagem)): ?>
        <p><?php echo $mensagem; ?></p>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="nome_categoria">Nome da Categoria:</label>
            <input type="text" class="form-control" id="nome_categoria" name="nome_categoria" required>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar Categoria</button>
    </form>
</div>

</body>
</html>
