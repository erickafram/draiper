<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Draper</title>

    <!-- Links para as bibliotecas do Bootstrap e ícones -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Link para o seu arquivo de estilo personalizado -->
    <link rel="stylesheet" href="/draper/assets/css/style_sistema.css">
</head>
<body>
<!-- Barra de navegação (navbar) pode ser incluída aqui ou em navbar.php -->
<!-- Exemplo de navbar simples -->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top"> <!-- Adicione a classe "fixed-top" aqui -->
    <div class="container">
        <a href="#">
            <img src="../assets/images/logo.png" alt="" width="80px" style="margin:10px 15px;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Div para envolver os menus à esquerda -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Menu à esquerda do navbar -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        Início
                    </a>
                </li>
                <?php
                // Verifique o nível de acesso do usuário
                $nivelAcesso = $_SESSION['nivel_acesso'];

                // Exiba menus à esquerda com base no nível de acesso
                if ($nivelAcesso == 'usuario') {
                    // Menu à esquerda para usuários comuns
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="usuarioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                    echo 'Menu do Usuário';
                    echo '</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="usuarioDropdown">';
                    echo '<li><a class="dropdown-item" href="pagina_usuario1.php">Item 1</a></li>';
                    echo '<li><a class="dropdown-item" href="pagina_usuario2.php">Item 2</a></li>';
                    // Adicione mais itens de menu conforme necessário
                    echo '</ul>';
                    echo '</li>';
                } elseif ($nivelAcesso == 'revendedor') {
                    // Menu à esquerda para revendedores
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="revendedorDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                    echo 'Produtos';
                    echo '</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="revendedorDropdown">';
                    echo '<li><a class="dropdown-item" href="produtos.php">Lista</a></li>';
                    // Adicione mais itens de menu conforme necessário
                    echo '</ul>';
                    echo '</li>';
                } elseif ($nivelAcesso == 'fornecedor') {
                    // Menu à esquerda para fornecedores
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="fornecedorDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                    echo 'Produtos';
                    echo '</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="fornecedorDropdown">';
                    echo '<li><a class="dropdown-item" href="cadastro_produto.php">Cadastrar</a></li>';
                    echo '<li><a class="dropdown-item" href="listar_produtos.php">Listar</a></li>';
                    // Adicione mais itens de menu conforme necessário
                    echo '</ul>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>

        <!-- Menu à direita do navbar -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <!-- Dropdown do Usuário Logado -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $_SESSION['nome_completo']; ?> <!-- Modificado para mostrar o nome completo do usuário -->
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#">Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php">Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tinymce@5.9.1/tinymce.min.js"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
</body>
</html>
<div style="padding-bottom:65px;"></div>
