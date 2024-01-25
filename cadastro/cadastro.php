<?php
include('../banco/db_connection.php');
// Incluir o arquivo de cabeçalho (header.php)
include('../includes/bibliotecas.php');

// Inicializar variáveis para exibir mensagens de erro/sucesso
$mensagem = "";
$erro = "";

// Função para fazer uma solicitação HTTP GET para a API e obter os dados do CNPJ
function getDadosCNPJ($cnpj) {
    // Limpar o CNPJ para remover pontos e hifens
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

    // URL da API com o CNPJ fornecido pelo usuário
    $api_url = "https://minhareceita.org/$cnpj";

    // Fazer a solicitação GET para a API
    $response = file_get_contents($api_url);

    // Verificar se a solicitação foi bem-sucedida
    if ($response !== false) {
        // Decodificar a resposta JSON
        $data = json_decode($response, true);
        return $data;
    } else {
        return false; // Erro na solicitação
    }
}

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Exibir o conteúdo de $_POST
    //echo "<pre>";
    //var_dump($_POST);
    //echo "</pre>";

    // Recuperar os dados do formulário
    $nome_completo = $_POST["nome_completo"] ?? '';
    $cpf_cnpj = $_POST["cpf_cnpj"] ?? '';
    $telefone = $_POST["telefone"] ?? '';
    $email = $_POST["email"] ?? '';
    $endereco = $_POST["endereco"] ?? '';
    $numero = $_POST["numero"] ?? '';
    $complemento = $_POST["complemento"] ?? '';
    $cidade = $_POST["cidade"] ?? '';
    $estado = $_POST["estado"] ?? '';
    $cep = $_POST["cep"] ?? '';
    $senha = $_POST["senha"] ?? '';
    $confirmar_senha = $_POST["confirmar_senha"] ?? '';

// Verificar se as senhas são iguais
    if ($senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem.";
    }

    // Obter o valor de 'tipo_cadastro' do POST
    $tipo_cadastro = $_POST["tipo_cadastro"] ?? '';
    //echo "Valor de tipo_cadastro: " . $tipo_cadastro;

    // Atribuir valor a 'nivel_acesso' com base em 'tipo_cadastro'
    if ($tipo_cadastro == "revendedor") {
        $nivel_acesso = "revendedor";
    } elseif ($tipo_cadastro == "fornecedor") {
        $nivel_acesso = "fornecedor";
    } elseif ($tipo_cadastro == "transportadora") {
        $nivel_acesso = "transportadora";
    } else {
        // Defina um valor padrão ou manipule o erro conforme necessário
        $nivel_acesso = "usuario"; // Valor padrão
    }

    // Validação dos dados (você pode adicionar mais validações conforme necessário)
    if (empty($cpf_cnpj) || empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        // Verificar se é CPF ou CNPJ
        $cpf_cnpj_value = preg_replace('/[^0-9]/', '', $cpf_cnpj);

        if (strlen($cpf_cnpj_value) == 11) {
            // É um CPF, portanto, definir os campos de razão social e nome fantasia como vazios
            $razao_social = "";
            $nome_fantasia = "";
        } elseif (strlen($cpf_cnpj_value) == 14) {
            // É um CNPJ, portanto, definir os campos de nome completo como vazios
            $nome_completo = "";
            $razao_social = $_POST["razao_social"] ?? '';
            $nome_fantasia = $_POST["nome_fantasia"] ?? '';
        } else {
            $erro = "CPF ou CNPJ inválido.";
        }

        if (empty($erro)) {
            // Inserir os dados na tabela de usuários
            $sql = "INSERT INTO usuarios (nome_completo, cpf_cnpj, telefone, email, endereco, numero, complemento, cidade, estado, cep, razao_social, nome_fantasia, nivel_acesso, senha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssssssssssssss", $nome_completo, $cpf_cnpj, $telefone, $email, $endereco, $numero, $complemento, $cidade, $estado, $cep, $razao_social, $nome_fantasia, $nivel_acesso, $senha);
                if ($stmt->execute()) {
                    $mensagem = "Cadastro realizado com sucesso!";
                } else {
                    $erro = "Erro ao cadastrar. Tente novamente. Erro: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $erro = "Erro na preparação da consulta. Erro: " . $mysqli->error;
            }
        }
    }
}
?>

<div id="loader" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
    <p>Carregando...</p>
</div>

<div id="conteudo" style="display: none;">

<!-- Conteúdo da página de cadastro -->
<div class="container mt-5" style="max-width: 800px;">
    <h4>Vamos concluir seu cadastro!</h4>

    <!-- Exibir mensagens de erro/sucesso -->
    <?php if (!empty($erro)) { ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php } ?>
    <?php if (!empty($mensagem)) { ?>
        <div class="alert alert-success">
            <?php echo $mensagem; ?>
            Redirecionando em 5 segundos...
        </div>
        <meta http-equiv="refresh" content="5;url=../login.php">
    <?php } ?>


    <!-- Formulário de cadastro -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="tipo_cadastro" value="<?php echo htmlspecialchars($_POST["tipo_cadastro"] ?? ''); ?>">

        <div class="mb-3">
            <label for="cpf_cnpj" class="form-label">CPF ou CNPJ</label>
            <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" placeholder="Digite o CPF ou CNPJ">
        </div>

        <!-- Campos de Nome Completo, Razão Social e Nome Fantasia com base na escolha do tipo de cadastro -->
        <div id="campos_nome_completo">
            <div class="mb-3">
                <label for="nome_completo" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome_completo" name="nome_completo">
            </div>
        </div>

        <div id="campos_razao_social" class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="razao_social" class="form-label">Razão Social</label>
                    <input type="text" class="form-control" id="razao_social" name="razao_social">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                    <input type="text" class="form-control" id="nome_fantasia" name="nome_fantasia">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div id="container_telefone" class="mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone">
                </div>
            </div>
            <div class="col-md-6">
                <div id="container_email" class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div id="container_cep" class="mb-3">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" placeholder="Digite apenas números">
                </div>
            </div>

            <div class="col-md-6">
                <div id="container_endereco" class="mb-3">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div id="container_numero" class="mb-3" style="display: none;">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" class="form-control" id="numero" name="numero">
                </div>
            </div>
            <div class="col-md-6">
                <div id="container_complemento" class="mb-3" style="display: none;">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" class="form-control" id="complemento" name="complemento">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div id="container_cidade" class="mb-3" style="display: none;">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade">
                </div>
            </div>
            <div class="col-md-6">
                <div id="container_estado" class="mb-3" style="display: none;">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado">
                </div>
            </div>
        </div>

        <div id="container_senha" class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha">
        </div>

        <div id="container_confirmar_senha" class="mb-3">
            <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha">
        </div>


        <a class="btn btn-secondary" href="tipo_cadastro.php">Voltar</a>
        <button type="submit" class="btn btn-primary" id="botao_cadastrar" style="display: none;">Faça parte da Draiper!</button>
    </form>
</div>
</div>

<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Incluir biblioteca Inputmask.js para máscaras de CPF e CNPJ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.5/jquery.inputmask.min.js"></script>

<!-- Seu código JavaScript -->
<script>
    $(document).ready(function () {
        // Inicialmente, oculte todos os contêineres de campos adicionais e o botão Cadastrar
        $("#campos_nome_completo, #campos_razao_social, #container_telefone, #container_email, #container_endereco, #container_cep, #container_numero, #container_complemento, #container_cidade, #container_estado, #container_senha, #container_confirmar_senha, #botao_cadastrar").hide();

        // Aplicar máscara de CPF ou CNPJ ao campo
        $("#cpf_cnpj").inputmask({
            mask: ["999.999.999-99", "99.999.999/9999-99"],
            keepStatic: true
        });

        // Aplicar máscara de telefone ao campo
        $("#telefone").inputmask({
            mask: ["(99) 9 9999-9999"],
            keepStatic: true
        });

        // Evento para buscar o endereço ao preencher o CEP
        $("#cep").on("change", function () {
            var cep = $(this).val().replace(/\D/g, ''); // Remover caracteres não numéricos

            if (cep.length != 8) {
                alert("CEP inválido. Digite apenas os números.");
                return;
            }

            // Fazer a solicitação AJAX para a API do ViaCEP
            $.ajax({
                url: "https://viacep.com.br/ws/" + cep + "/json/",
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (!data.erro) {
                        // Preencher os campos de endereço com os dados da API
                        $("#endereco").val(data.logradouro);
                        $("#cidade").val(data.localidade);
                        $("#estado").val(data.uf);
                        // Você pode preencher outros campos, como bairro, cidade, etc., se necessário
                    } else {
                        alert("CEP não encontrado.");
                    }
                },
                error: function () {
                    alert("Erro ao buscar o CEP. Tente novamente mais tarde.");
                }
            });
        });

        // Evento para mostrar/ocultar campos adicionais com base no tipo de cadastro (CPF ou CNPJ)
        $("#cpf_cnpj").on("input", function () {
            var cpfCnpjValue = $(this).val().replace(/[^0-9]/g, ''); // Remover caracteres não numéricos

            if (cpfCnpjValue.length == 11) {
                // CPF válido, mostre os campos relevantes e o botão Cadastrar
                $("#campos_nome_completo").show();
                $("#container_telefone, #container_email, #container_endereco, #container_cep, #container_numero, #container_complemento, #container_cidade, #container_estado, #container_senha, #container_confirmar_senha, #botao_cadastrar").show();
            } else if (cpfCnpjValue.length == 14) {
                // CNPJ válido, mostre os campos relevantes e o botão Cadastrar
                $("#campos_razao_social").show();
                $("#container_telefone, #container_email, #container_endereco, #container_cep, #container_numero, #container_complemento, #container_cidade, #container_estado, #container_senha, #container_confirmar_senha, #botao_cadastrar").show();
            } else {
                // Tamanho inválido, oculte todos os contêineres de campos adicionais e o botão Cadastrar
                $("#campos_nome_completo, #campos_razao_social, #container_telefone, #container_email, #container_endereco, #container_cep, #container_numero, #container_complemento, #container_cidade, #container_estado, #container_senha, #container_confirmar_senha, #botao_cadastrar").hide();
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        // Evento para buscar os dados do CNPJ ao preencher o campo
        $("#cpf_cnpj").on("blur", function () {
            var cnpj = $(this).val().replace(/[^0-9]/g, ''); // Remover pontos e hifens

            if (cnpj.length === 14) {
                // Fazer a solicitação AJAX para a API
                $.ajax({
                    url: "https://minhareceita.org/" + cnpj,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        if (data.razao_social && data.nome_fantasia) {
                            // Preencher os campos de Razão Social e Nome Fantasia com os dados da API
                            $("#razao_social").val(data.razao_social);
                            $("#nome_fantasia").val(data.nome_fantasia);
                        } else {
                            alert("CNPJ não encontrado ou não possui Razão Social e Nome Fantasia.");
                        }
                    },
                    error: function () {
                        alert("Erro ao buscar os dados do CNPJ. Verifique se o CNPJ é válido e tente novamente.");
                    }
                });
            }
        });
    });
</script>

<script>
    $(window).on('load', function() {
        // Esconde o indicador de carregamento e mostra o conteúdo da página
        $('#loader').fadeOut('slow', function() {
            $('#conteudo').fadeIn();
        });
    });
</script>
