<?php
// Inclua o arquivo de conexão com o banco de dados
include('banco/db_connection.php');

// Inicie a sessão
session_start();

// Inicialize variáveis para exibir mensagens de erro/sucesso
$mensagem = "";
$erro = "";

// Verifique se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupere os dados do formulário
    $usuario = $_POST["usuario"];
    $senha = $_POST["senha"];

    // Execute a consulta SQL para verificar as credenciais do usuário
    $sql = "SELECT * FROM usuarios WHERE email = ? AND senha = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $usuario, $senha);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Credenciais corretas, faça o login do usuário
            $row = $result->fetch_assoc();
            $_SESSION['logged_in'] = true;
            $_SESSION['usuario'] = $usuario;
            $_SESSION['usuario_id'] = $row['id']; // Armazenar o ID do usuário na sessão
            $_SESSION['nivel_acesso'] = $row['nivel_acesso']; // Obtenha o nível de acesso do banco de dados
            $_SESSION['nome_completo'] = $row['nome_completo'];

            // Redirecione com base no nível de acesso
            if ($row['nivel_acesso'] == 'admin') {
                header("Location: admin/dashboard.php");
                exit();
            } elseif ($row['nivel_acesso'] == 'revendedor') {
                header("Location: revendedor/dashboard.php");
                exit();
            } elseif ($row['nivel_acesso'] == 'fornecedor') {
                header("Location: fornecedor/dashboard.php");
                exit();
            } elseif ($row['nivel_acesso'] == 'transportadora') {
                header("Location: transportadora/dashboard.php");
                exit();
            } else {
                header("Location: perfil.php"); // Redirecionar para a página de perfil padrão ou outra página de destino
                exit();
            }
        } else {
            $erro = "Usuário ou senha incorretos. Tente novamente.";
        }

        $stmt->close();
    } else {
        $erro = "Erro na preparação da consulta.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        .input-group-text {
            background-color: #fdfdfd !important;
            color: #36a9e1 !important;
            border: 1px solid #ced4da !important;
        }

        body{
            background-color: #ffffff !important;
        }
    </style>
</head>
<body>
<!-- Inclua o arquivo header.php -->
<?php include('includes/bibliotecas.php'); ?>

<div class="container mt-5">
    <div class="login-container" style="padding:30px;">
        <center>
            <img src="assets/images/logo.png" alt="Logo" style="max-width: 120px; margin-bottom:10px;">
            <h4></h4>
            <span>É um prazer te ver por aqui!</span>
            <p>
                <span>Faça Login e aproveite a <b>Draper!</b></span>
            </p>
        </center>

        <!-- Exibir mensagens de erro -->
        <?php if (!empty($erro)) { ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php } ?>

        <!-- Formulário de login -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3 input-group" style="padding-top:20px;">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                </div>
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Email" required>
            </div>
            <div class="mb-3 input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="bi bi bi-lock-fill font-weight-bold"></i></span>
                </div>
                <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha" required>
            </div>
            <center><button type="submit" class="btn btn-primary">Entrar</button></center>
        </form>
        <center><p style="padding-top:15px; font-size: 14px;">Ainda não tem uma conta na Draper? <b><a href="cadastro/tipo_cadastro.php">Registre-se agora</a></b></p></center>

    </div>
</div>
</body>
</html>
