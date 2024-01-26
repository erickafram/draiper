<?php
// Incluir o arquivo de cabeçalho (header.php)
include('../includes/bibliotecas.php');
?>

<style>
    .card:hover {
        border-color: #1acc8d !important;
    }

</style>


<div class="container mt-5" style="max-width: 800px;">
    <h1 class="text-center" style="font-size: 25px;">É um prazer te ver por aqui!</h1>
    <p class="text-center" style="font-size: 20px;">Quem é você na Draiper?</p>

    <form action="cadastro.php" method="POST">
        <div class="row">
            <!-- Opção 1: Sou Revendedor -->
            <div class="col-12 col-md-4">
                <div class="card text-center clickable-card">
                    <div class="card-body">
                        <h5 class="card-title">Sou Revendedor</h5>
                        <p class="card-text">Selecione esta opção se você deseja se cadastrar como revendedor.</p>
                        <input type="radio" id="revendedor" name="tipo_cadastro" value="revendedor" required>
                    </div>
                </div>
            </div>

            <!-- Opção 2: Sou Fornecedor -->
            <div class="col-12 col-md-4">
                <div class="card text-center clickable-card">
                    <div class="card-body">
                        <h5 class="card-title">Sou Fornecedor</h5>
                        <p class="card-text">Selecione esta opção se você deseja se cadastrar como fornecedor.</p>
                        <input type="radio" id="fornecedor" name="tipo_cadastro" value="fornecedor" required>
                    </div>
                </div>
            </div>

            <!-- Opção 3: Sou Transportadora -->
            <div class="col-12 col-md-4">
                <div class="card text-center clickable-card">
                    <div class="card-body">
                        <h5 class="card-title">Sou Transportadora</h5>
                        <p class="card-text">Selecione esta opção se você deseja se cadastrar como transportadora.</p>
                        <input type="radio" id="transportadora" name="tipo_cadastro" value="transportadora" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <a class="btn btn-secondary" href="../index.php">Voltar</a>
            <button type="submit" class="btn btn-primary btn-lg">Avançar</button>
        </div>
    </form>
</div>

<script>
    document.querySelectorAll('.clickable-card').forEach(card => {
        card.addEventListener('click', function() {
            let radio = this.querySelector('input[type="radio"]');
            radio.checked = true;

            // Remover a classe 'selected' de todos os outros cards
            document.querySelectorAll('.clickable-card').forEach(c => c.classList.remove('selected'));

            // Adicionar a classe 'selected' ao card clicado
            this.classList.add('selected');
        });
    });
</script>
