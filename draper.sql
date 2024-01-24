-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 22/01/2024 às 13:30
-- Versão do servidor: 11.2.2-MariaDB
-- Versão do PHP: 8.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `draper`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(34, 'Blusas'),
(35, 'Calças'),
(36, 'Camisetas'),
(37, 'Vestidos'),
(38, 'Saias'),
(39, 'Jaquetas'),
(40, 'Shorts'),
(41, 'Bermudas'),
(42, 'Lingerie'),
(43, 'Pijamas'),
(44, 'Macacão'),
(45, 'Blazer'),
(46, 'Crop Top'),
(47, 'Body'),
(48, 'Cinto'),
(49, 'Calçados'),
(50, 'Acessórios'),
(51, 'Meias'),
(52, 'Moletons');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `fornecedor_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `categoria` int(11) NOT NULL,
  `tamanho` varchar(20) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagens` text DEFAULT NULL,
  `estoque` int(11) NOT NULL,
  `cores_disponiveis` text DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `composicao_material` text DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `instrucoes_cuidado` text DEFAULT NULL,
  `tempo_processamento_envio` varchar(255) DEFAULT NULL,
  `politica_devolucao` text DEFAULT NULL,
  `codigo_barras_sku` varchar(255) DEFAULT NULL,
  `tags_palavras_chave` text DEFAULT NULL,
  `disponibilidade` date DEFAULT NULL,
  `quantidade_minima_pedido` int(11) DEFAULT NULL,
  `imagem_destaque` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `fornecedor_id`, `nome`, `descricao`, `categoria`, `tamanho`, `preco`, `imagens`, `estoque`, `cores_disponiveis`, `marca`, `composicao_material`, `peso`, `instrucoes_cuidado`, `tempo_processamento_envio`, `politica_devolucao`, `codigo_barras_sku`, `tags_palavras_chave`, `disponibilidade`, `quantidade_minima_pedido`, `imagem_destaque`) VALUES
(81, 7, 'Blusas', 'Blusas', 34, 'M,G', 150.00, 'imagens_produto/1705859060-destaque.jpg,imagens_produto/1705859060-Destaques.jpg', 100, 'Branco, Preto', 'Lorrany', 'Algodão', 10.00, 'Não pode lavar com agua', '3-5 dias úteis', '7 Dias', '', 'Roupa', '2024-01-21', 10, 'imagens_produto/1705859060-blusa.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(255) NOT NULL,
  `cpf_cnpj` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `razao_social` varchar(255) DEFAULT NULL,
  `nome_fantasia` varchar(255) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `nivel_acesso` enum('usuario','revendedor','fornecedor','transportadora','gestor','administrador') NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome_completo`, `cpf_cnpj`, `telefone`, `email`, `endereco`, `cep`, `razao_social`, `nome_fantasia`, `numero`, `complemento`, `cidade`, `estado`, `nivel_acesso`, `senha`) VALUES
(7, 'Erick Vinicius Rodrigues', '017.588.481-11', '(62) 9 8101-3083', 'erickafram08@gmail.com', 'Quadra ARSE 102 Alameda 8', '77023570', '', '', '', '', 'Palmas', 'TO', 'fornecedor', '123456');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fornecedor_id` (`fornecedor_id`),
  ADD KEY `categoria` (`categoria`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf_cnpj` (`cpf_cnpj`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`fornecedor_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `produtos_ibfk_2` FOREIGN KEY (`categoria`) REFERENCES `categorias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
