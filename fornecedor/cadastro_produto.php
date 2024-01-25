<?php
session_start();
include('../verificar_sessao.php');
include('../banco/db_connection.php');

$mensagem = "";
$erro = "";

if (isset($_SESSION['usuario_id'])) {
    $fornecedor_id = $_SESSION['usuario_id'];
} else {
    $erro = "Usuário não identificado. Faça o login novamente.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($erro)) {
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

    $preco = $_POST['preco'] ?? '0.0';
    $preco = str_replace(',', '.', $preco); // Substituir vírgulas por pontos
    $preco = preg_replace("/[^0-9\.]/", "", $preco); // Remover todos os caracteres exceto números e ponto
    $preco = floatval($preco); // Converter para float


    // Processamento do Upload da Imagem em Destaque
    $pastaUpload = "imagens_produto/";
    if (isset($_FILES['imagem_destaque']) && $_FILES['imagem_destaque']['error'] == 0) {
        $nomeArquivoDestaque = time() . '-' . $_FILES['imagem_destaque']['name'];
        $caminhoTempDestaque = $_FILES['imagem_destaque']['tmp_name'];
        $caminhoSalvarDestaque = $pastaUpload . basename($nomeArquivoDestaque);

        if (move_uploaded_file($caminhoTempDestaque, $caminhoSalvarDestaque)) {
            $imagemDestaque = $caminhoSalvarDestaque;
        } else {
            $erro = "Houve um erro ao fazer o upload da imagem em destaque.";
        }
    }
    //echo "<pre>";
    //print_r($_FILES);
    //echo "</pre>";

// Substitua as linhas anteriores por estas
    if (isset($_POST['tamanhos']) && is_array($_POST['tamanhos'])) {
        $tamanhoString = implode(",", $_POST['tamanhos']);
    } else {
        $tamanhoString = '';
    }

    // Processamento do Upload de Imagens
    $pastaUpload = "imagens_produto/";
    $imagens = array();

// Verifica se o array 'imagens' está definido e não é nulo
    if (isset($_FILES['imagens']) && $_FILES['imagens']['name'][0] != '') {
        $totalImagens = count($_FILES['imagens']['name']);

        for ($i = 0; $i < $totalImagens; $i++) {
            if ($i >= 6) {
                // Se mais de 6 imagens forem enviadas, interrompa o loop
                break;
            }

            $nomeArquivo = time() . '-' . $_FILES['imagens']['name'][$i]; // Adicionando timestamp para unicidade
            $caminhoTemp = $_FILES['imagens']['tmp_name'][$i];
            $caminhoSalvar = $pastaUpload . basename($nomeArquivo);

            if (move_uploaded_file($caminhoTemp, $caminhoSalvar)) {
                $imagens[] = $caminhoSalvar;
            } else {
                $erro = "Houve um erro ao fazer o upload da imagem.";
            }
        }
    } else {
        $erro = "Nenhuma imagem selecionada.";
    }

    if (empty($erro) && !empty($imagens)) {
        $imagensString = implode(",", $imagens);

        // Inserir no banco de dados
        $sql = "INSERT INTO produtos (fornecedor_id, nome, descricao, categoria, tamanho, preco, estoque, imagens, cores_disponiveis, marca, composicao_material, peso, instrucoes_cuidado, tempo_processamento_envio, politica_devolucao, codigo_barras_sku, tags_palavras_chave, disponibilidade, quantidade_minima_pedido, imagem_destaque) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("issssidssssdssssssis", $fornecedor_id, $nome, $descricao, $categoria, $tamanhoString, $preco, $estoque, $imagensString, $cores_disponiveis, $marca, $composicao_material, $peso, $instrucoes_cuidado, $tempo_processamento_envio, $politica_devolucao, $codigo_barras_sku, $tags_palavras_chave, $disponibilidade, $quantidade_minima_pedido, $imagemDestaque);

            if ($stmt->execute()) {
                $mensagem = "Produto cadastrado com sucesso!";
            } else {
                $erro = "Erro ao cadastrar o produto: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $erro = "Erro na preparação da consulta: " . $mysqli->error;
        }
    }
}

// Buscar categorias do banco de dados
$sqlCategorias = "SELECT id, nome FROM categorias";
$resultadoCategorias = $mysqli->query($sqlCategorias);
$categorias = [];
if ($resultadoCategorias) {
    while ($linha = $resultadoCategorias->fetch_assoc()) {
        $categorias[] = $linha;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto</title>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

        <?php
        if (!empty($mensagem)) {
            echo '<div class="alert alert-success">' . $mensagem . '</div>';
        }
        if (!empty($erro)) {
            echo '<div class="alert alert-danger">' . $erro . '</div>';
        }
        ?>

        <!-- Campo para Upload da Imagem em Destaque -->
        <div class="form-group" style="border:1px solid #eee; padding:20px;">
            <label for="imagem_destaque">Imagem em Destaque do Produto:</label>
            <input type="file" class="form-control" id="imagem_destaque" name="imagem_destaque" accept="image/*">
            <div id="preview-destaque-container" style="padding:5px 10px;"></div>
        </div>

        <!-- Caixa para Adicionar Imagens -->
        <div class="form-group" style="border: 1px solid #eee; padding: 20px;">
            <label>Imagens do Produto:</label>
            <div class="image-upload-box">
                <input type="file" class="image-upload" name="imagens[]" accept="image/*" style="display: none;" multiple>
                <button type="button" class="add-image-button btn btn-primary">Adicionar Imagem</button>
            </div>
            <small>(Até 6 imagens permitidas)</small>
            <div id="preview-container" style="padding: 5px 10px;"></div>
            <div id="image-count" style="margin-top: 10px;"></div>
        </div>



        <!-- Informações Básicas -->
        <div class="form-group" style="border:1px solid #eee; padding:20px;">
            <center><h6 style="padding-bottom:15px;">Informações Básicas</h6></center>


            <label for="categoria">Categoria:</label>
            <select class="form-control" id="categoria" name="categoria" onchange="alterarOpcoesDeTamanho()">
                <option value="">Selecione</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nome']; ?></option>
                <?php endforeach; ?>
            </select>
            <div style="padding-bottom:20px;"></div>


            <div class="row">
                <div class="col-md-6" style="padding-bottom:20px;">
                    <!-- Campo para o Nome do Produto -->
                    <label for="nome">Nome do Produto:</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="col-md-6">
                    <!-- Campo para a Descrição -->
                    <label for="descricao">Descrição:</label>
                    <textarea class="form-control" id="descricao" name="descricao" style="margin-bottom:5px;" required></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6" style="padding-bottom:20px;">
                    <!-- Campo para o Tamanho -->
                    <label for="tamanho">Tamanhos Disponivéis:</label>
                    <div id="tamanho">
                    </div>
                </div>

                <div class="col-md-6" style="padding-bottom:20px;">
                    <!-- Campo para o Preço -->
                    <label for="preco">Preço unitário:</label>
                    <input type="text" class="form-control" id="preco" name="preco" required>
                </div>
            </div>

            <!-- Campo para o Estoque -->
            <label for="estoque">Quantidade em Estoque:</label>
            <input type="number" class="form-control" id="estoque" name="estoque" required>
        </div>

        <!-- Detalhes do Produto -->
        <div class="form-group" style="border:1px solid #eee; padding:20px;">
            <center><h6 style="padding-bottom:15px;">Detalhes do Produto</h6></center>

            <div class="form-group">
                <!-- Campo onde as cores selecionadas serão exibidas -->
                <label for="cores_selecionadas">Cores Disponíveis:</label>
                <input type="text" class="form-control mb-2" id="cores_selecionadas" name="cores_disponiveis" placeholder="Selecione as cores abaixo" readonly>

                <!-- Dropdown de seleção de cores -->
                <select id="seletor_cores" class="form-control mb-2">
                    <option value="">Selecione uma cor</option>
                    <option value="Amarelo">Amarelo</option>
                    <option value="Azul Claro">Azul Claro</option>
                    <option value="Azul Marinho">Azul Marinho</option>
                    <option value="Azul Turquesa">Azul Turquesa</option>
                    <option value="Bege">Bege</option>
                    <option value="Branco">Branco</option>
                    <option value="Burgundy">Burgundy</option>
                    <option value="Cinza">Cinza</option>
                    <option value="Dourado">Dourado</option>
                    <option value="Laranja">Laranja</option>
                    <option value="Lilás">Lilás</option>
                    <option value="Marrom">Marrom</option>
                    <option value="Preto">Preto</option>
                    <option value="Prata">Prata</option>
                    <option value="Rosa">Rosa</option>
                    <option value="Roxo">Roxo</option>
                    <option value="Verde Escuro">Verde Escuro</option>
                    <option value="Verde Oliva">Verde Oliva</option>
                    <option value="Vermelho">Vermelho</option>
                </select>
                <!-- Botão para limpar seleção -->
                <button type="button" class="btn btn-secondary" id="limpar_selecao">Limpar Seleção</button>
            </div>



            <div class="row">
                <div class="col-md-6 detalhes">
                    <label for="marca">Marca:</label>
                    <input type="text" class="form-control" id="marca" name="marca">
                </div>

                <div class="col-md-6 detalhes">
                    <label for="composicao_material">Composição do Material:</label>
                    <input type="text" class="form-control" id="composicao_material" name="composicao_material">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 detalhes">
                    <!-- Peso -->
                    <label for="peso">Peso:</label>
                    <input type="number" class="form-control" id="peso" name="peso" step="0.01">
                </div>
                <div class="col-md-6">
                    <!-- Tempo de Processamento e Envio -->
                    <label for="tempo_processamento_envio">Tempo de Processamento e Envio:</label>
                    <select class="form-control" id="tempo_processamento_envio" name="tempo_processamento_envio">
                        <option value="1-2 dias úteis">1-2 dias úteis</option>
                        <option value="3-5 dias úteis">3-7 dias úteis</option>
                    </select>
                </div>


                <!-- Instruções de Cuidado (área de texto) -->
                <div class="form-group">
                    <label for="instrucoes_cuidado">Instruções de Cuidado:</label>
                    <textarea class="form-control" id="instrucoes_cuidado" name="instrucoes_cuidado"></textarea>
                </div>

                <!-- Política de Devolução (área de texto) -->
                <div class="form-group">
                    <label for="politica_devolucao">Política de Devolução:</label>
                    <textarea class="form-control" id="politica_devolucao" name="politica_devolucao"></textarea>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="form-group" style="border:1px solid #eee; padding:20px;">
                <center><h6 style="padding-bottom:15px;">Informações Adicionais</h6></center>

                <div class="row">
                    <div class="col-md-6 detalhes">
                        <!-- Código de Barras ou SKU -->
                        <label for="codigo_barras_sku">Código de Barras/SKU:</label>
                        <input type="text" class="form-control" id="codigo_barras_sku" name="codigo_barras_sku">
                    </div>
                    <div class="col-md-6 detalhes">
                        <!-- Tags ou Palavras-chave -->
                        <label for="tags_palavras_chave">Tags/Palavras-chave:</label>
                        <input type="text" class="form-control" id="tags_palavras_chave" name="tags_palavras_chave">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 detalhes">
                        <!-- Disponibilidade -->
                        <label for="disponibilidade">Disponibilidade:</label>
                        <input type="date" class="form-control" id="disponibilidade" name="disponibilidade">
                    </div>
                    <div class="col-md-6 detalhes">
                        <!-- Quantidade Mínima de Pedido -->
                        <label for="quantidade_minima_pedido">Quantidade Mínima de Pedido:</label>
                        <input type="number" class="form-control" id="quantidade_minima_pedido" name="quantidade_minima_pedido">
                    </div>
                </div>
            </div>
            <!-- Botão de Submissão -->
            <button type="submit" class="btn btn-primary" style="margin-top:20px;">Cadastrar Produto</button>
    </form>
    <script>

        document.getElementById('seletor_cores').addEventListener('change', function() {
            var coresSelecionadasInput = document.getElementById('cores_selecionadas');
            var corSelecionada = this.value;

            if (corSelecionada) {
                // Adiciona a cor selecionada ao campo, separadas por vírgula
                if (coresSelecionadasInput.value) {
                    coresSelecionadasInput.value += ', ' + corSelecionada;
                } else {
                    coresSelecionadasInput.value = corSelecionada;
                }
            }

            // Limpa o seletor para permitir outra seleção
            this.value = '';
        });

        document.getElementById('limpar_selecao').addEventListener('click', function() {
            // Limpa o campo de cores selecionadas
            document.getElementById('cores_selecionadas').value = '';
        });

        document.getElementById('preco').addEventListener('input', function (e) {
            var value = e.target.value.replace(/\D/g, '');
            value = value.replace(/^0+/, ''); // Remove zeros à esquerda
            if (value.length <= 5) {
                value = value.padStart(3, '0'); // Garante pelo menos 3 dígitos
                value = value.replace(/(\d{2})$/, ',$1'); // Insere vírgula antes dos últimos 2 dígitos
                if (value.length > 4) {
                    value = value.replace(/^(\d{1,2})(\d{3,})/, '$1.$2'); // Insere ponto nos milhares
                }
                e.target.value = 'R$ ' + value;
            } else {
                e.target.value = e.target.value.substring(0, e.target.value.length - 1); // Evita ultrapassar o limite
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const inputDestaque = document.getElementById('imagem_destaque');
            const previewDestaqueContainer = document.getElementById('preview-destaque-container');

            inputDestaque.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        previewDestaqueContainer.innerHTML = ''; // Limpa o contêiner antes de adicionar nova imagem
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.style.width = '70px'; // Define o tamanho da pré-visualização
                        img.style.height = '70px';
                        previewDestaqueContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const imageUploadBox = document.querySelector('.image-upload-box');
            const imageUploadInput = document.querySelector('.image-upload');
            const addImageButton = document.querySelector('.add-image-button');
            const previewContainer = document.getElementById('preview-container');
            const imageCount = document.getElementById('image-count');

            let uploadedImages = 0;

            addImageButton.addEventListener('click', function () {
                imageUploadInput.click();
            });

            imageUploadInput.addEventListener('change', handleImageUpload);

            function handleImageUpload(event) {
                const files = event.target.files;
                for (let i = 0; i < files.length; i++) {
                    if (uploadedImages >= 6) break;

                    const reader = new FileReader();
                    reader.onload = function (event) {
                        const imgContainer = document.createElement('div');
                        imgContainer.className = 'img-container';
                        imgContainer.style.position = 'relative';
                        imgContainer.style.display = 'inline-block';
                        imgContainer.style.marginRight = '10px';

                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.style.width = '70px';
                        img.style.height = '70px';
                        img.classList.add('uploaded-image');

                        const deleteIcon = document.createElement('span');
                        deleteIcon.innerHTML = '&#10006;';
                        deleteIcon.style.position = 'absolute';
                        deleteIcon.style.top = '5px';
                        deleteIcon.style.right = '5px';
                        deleteIcon.style.cursor = 'pointer';
                        deleteIcon.style.color = 'red';
                        deleteIcon.onclick = function () {
                            imgContainer.remove();
                            uploadedImages--;
                            updateImageCount();
                        };

                        imgContainer.appendChild(img);
                        imgContainer.appendChild(deleteIcon);
                        previewContainer.appendChild(imgContainer);
                        uploadedImages++;
                        updateImageCount();
                    };
                    reader.readAsDataURL(files[i]);
                }
            }

            // Função para atualizar o contador de imagens
            function updateImageCount() {
                imageCount.innerHTML = `(${uploadedImages}/6) imagens selecionadas`;
            }
        });


    </script>

    <script>
        function alterarOpcoesDeTamanho() {
            var categoriaSelecionada = document.getElementById("categoria").value;
            var containerTamanho = document.getElementById("tamanho");

            // Limpa as opções existentes
            containerTamanho.innerHTML = '';

            // Define os tamanhos para cada categoria
            var tamanhosPorCategoria = {
                "34": ["P", "M", "G", "GG"],  // Blusas
                "35": ["36", "38", "40", "42", "44"],  // Calças
                "36": ["P", "M", "G", "GG"],  // Camisetas
                "37": ["P", "M", "G", "GG", "XG"],  // Vestidos
                "38": ["PP", "P", "M", "G"],  // Saias
                "39": ["P", "M", "G", "GG"],  // Jaquetas
                "40": ["P", "M", "G", "GG"],  // Shorts
                "41": ["38", "40", "42", "44"],  // Bermudas
                "42": ["P", "M", "G"],  // Lingerie
                "43": ["P", "M", "G", "GG"],  // Pijamas
                "44": ["P", "M", "G", "GG"],  // Macacão
                "45": ["P", "M", "G", "GG"],  // Blazer
                "46": ["P", "M", "G"],  // Crop Top
                "47": ["P", "M", "G"],  // Body
                "48": ["Único"],  // Cinto
                "49": ["36", "38", "40", "42"],  // Calçados
                "50": ["Único"],  // Acessórios
                "51": ["Único"],  // Meias
                "52": ["P", "M", "G", "GG"]  // Moletons
            };

            var tamanhos = tamanhosPorCategoria[categoriaSelecionada] || [];

            // Adiciona as caixas de seleção de tamanho
            tamanhos.forEach(function(tamanho) {
                var label = document.createElement("label");
                label.innerHTML = tamanho;

                var checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.name = "tamanhos[]";
                checkbox.value = tamanho;

                label.prepend(checkbox);
                containerTamanho.appendChild(label);
            });
        }
    </script>
    <a id="scroll-to-top" href="#header-section" class="scroll-to-top">
        <i class="mdi mdi-chevron-up"></i>
    </a>
</body>
</html>
