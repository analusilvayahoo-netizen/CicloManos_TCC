-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/08/2026 às 13:22
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ciclomanos`
--
CREATE DATABASE IF NOT EXISTS `ciclomanos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ciclomanos`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `bairros`
--

DROP TABLE IF EXISTS `bairros`;
CREATE TABLE `bairros` (
  `id_bairro` int(11) NOT NULL,
  `nome_bairro` varchar(100) NOT NULL,
  `id_cidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(100) NOT NULL,
  `nome_categoria` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nome_categoria`) VALUES
(1, 'Mountain Bike');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cicloprodutos`
--

DROP TABLE IF EXISTS `cicloprodutos`;
CREATE TABLE `cicloprodutos` (
  `id` int(10) NOT NULL,
  `produto` varchar(100) NOT NULL,
  `descricao` varchar(300) NOT NULL,
  `imagem` varchar(1000) NOT NULL,
  `id_marca` int(11) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `preco_venda` decimal(5,2) NOT NULL,
  `qtd_atual` int(100) NOT NULL,
  `estoque_minimo` int(100) NOT NULL,
  `id_categoria` int(100) NOT NULL,
  `modalidade` int(100) NOT NULL,
  `tamanho_aro` int(100) NOT NULL,
  `material` int(100) NOT NULL,
  `cor` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cicloprodutos`
--

INSERT INTO `cicloprodutos` (`id`, `produto`, `descricao`, `imagem`, `id_marca`, `modelo`, `preco_venda`, `qtd_atual`, `estoque_minimo`, `id_categoria`, `modalidade`, `tamanho_aro`, `material`, `cor`) VALUES
(6, 'Bicicleta Caloi Elite Carbon Sport', 'Bicicleta de alta performance aro 29, ideal para trilhas, passeios e competições, com quadro em carbono, suspensão dianteira e transmissão de 12 velocidades.', 'caloi_elite_carbon_sport.jpg', 1, 'Elite Carbon Sport 2026', 999.99, 10, 2, 1, 0, 29, 0, 0),
(11, 'Bicicleta Aro 29 Aço Carbono Freios A Disco Suspensão 21 ', 'A bicicleta aro 29 em aço carbono da marca KGT é ideal para adultos que buscam um passeio confortável e seguro. Com freios a disco mecânico dianteiro e traseiro, proporciona uma frenagem eficiente em qualquer situação. Com 21 velocidades, permite ajustar a marcha de acordo com o terreno, garantindo ', 'https://http2.mlstatic.com/D_NQ_NP_2X_872071-MLB91746760832_092025-F-bicicleta-aro-29-aco-carbono-freios-a-disco-suspenso-21-vel.webp', 1, ' KGT Bikes', 0.00, 6, 2, 1, 0, 29, 0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidades`
--

DROP TABLE IF EXISTS `cidades`;
CREATE TABLE `cidades` (
  `id_cidade` int(11) NOT NULL,
  `nome_cidade` varchar(100) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `id_dado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dados_pessoais`
--

DROP TABLE IF EXISTS `dados_pessoais`;
CREATE TABLE `dados_pessoais` (
  `id_dado` int(11) NOT NULL,
  `cpf` char(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `id_endereco` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `enderecos`
--

DROP TABLE IF EXISTS `enderecos`;
CREATE TABLE `enderecos` (
  `id_endereco` int(11) NOT NULL,
  `rua` varchar(150) NOT NULL,
  `cep` char(8) NOT NULL,
  `id_bairro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estados`
--

DROP TABLE IF EXISTS `estados`;
CREATE TABLE `estados` (
  `id_estado` int(11) NOT NULL,
  `nome_estado` varchar(50) NOT NULL,
  `uf` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

DROP TABLE IF EXISTS `funcionarios`;
CREATE TABLE `funcionarios` (
  `id_funcionario` int(11) NOT NULL,
  `id_dado` int(11) NOT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `data_admissao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_venda`
--

DROP TABLE IF EXISTS `itens_venda`;
CREATE TABLE `itens_venda` (
  `id_item` int(11) NOT NULL,
  `id_venda` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `manutencao`
--

DROP TABLE IF EXISTS `manutencao`;
CREATE TABLE `manutencao` (
  `id_manutencao` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `data_entrada` date DEFAULT NULL,
  `entrega_estimada` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `marcas`
--

DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL,
  `nome_marca` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `marcas`
--

INSERT INTO `marcas` (`id_marca`, `nome_marca`) VALUES
(1, 'Shimano');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pecas`
--

DROP TABLE IF EXISTS `pecas`;
CREATE TABLE `pecas` (
  `id_peca` int(11) NOT NULL,
  `descricao` varchar(150) NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

DROP TABLE IF EXISTS `servicos`;
CREATE TABLE `servicos` (
  `id_servico` int(11) NOT NULL,
  `nome_servico` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `valor_base` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servico_manutencao`
--

DROP TABLE IF EXISTS `servico_manutencao`;
CREATE TABLE `servico_manutencao` (
  `id_manutencao` int(11) NOT NULL,
  `id_servico` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `valor_unitario` decimal(10,2) NOT NULL,
  `valor_total` decimal(10,2) GENERATED ALWAYS AS (`quantidade` * `valor_unitario`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `telefones`
--

DROP TABLE IF EXISTS `telefones`;
CREATE TABLE `telefones` (
  `id_telefone` int(11) NOT NULL,
  `numero_telefone` char(11) NOT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `id_dado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

DROP TABLE IF EXISTS `vendas`;
CREATE TABLE `vendas` (
  `id_venda` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `data` date NOT NULL,
  `valor_total` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `bairros`
--
ALTER TABLE `bairros`
  ADD PRIMARY KEY (`id_bairro`),
  ADD KEY `id_cidade` (`id_cidade`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `cicloprodutos`
--
ALTER TABLE `cicloprodutos`
  ADD PRIMARY KEY (`id`,`id_marca`) USING BTREE,
  ADD KEY `fk_produtos_marca` (`id_marca`),
  ADD KEY `fk_produtos_categoria` (`id_categoria`);

--
-- Índices de tabela `cidades`
--
ALTER TABLE `cidades`
  ADD PRIMARY KEY (`id_cidade`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `id_dado` (`id_dado`);

--
-- Índices de tabela `dados_pessoais`
--
ALTER TABLE `dados_pessoais`
  ADD PRIMARY KEY (`id_dado`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `id_endereco` (`id_endereco`);

--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id_endereco`),
  ADD KEY `id_bairro` (`id_bairro`);

--
-- Índices de tabela `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id_estado`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id_funcionario`),
  ADD UNIQUE KEY `id_dado` (`id_dado`);

--
-- Índices de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_venda` (`id_venda`),
  ADD KEY `id` (`id`);

--
-- Índices de tabela `manutencao`
--
ALTER TABLE `manutencao`
  ADD PRIMARY KEY (`id_manutencao`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id_marca`);

--
-- Índices de tabela `pecas`
--
ALTER TABLE `pecas`
  ADD PRIMARY KEY (`id_peca`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id_servico`);

--
-- Índices de tabela `servico_manutencao`
--
ALTER TABLE `servico_manutencao`
  ADD PRIMARY KEY (`id_manutencao`,`id_servico`),
  ADD KEY `idx_servico_manutencao_servico` (`id_servico`);

--
-- Índices de tabela `telefones`
--
ALTER TABLE `telefones`
  ADD PRIMARY KEY (`id_telefone`),
  ADD KEY `id_dado` (`id_dado`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id_venda`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `bairros`
--
ALTER TABLE `bairros`
  MODIFY `id_bairro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `cicloprodutos`
--
ALTER TABLE `cicloprodutos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `cidades`
--
ALTER TABLE `cidades`
  MODIFY `id_cidade` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dados_pessoais`
--
ALTER TABLE `dados_pessoais`
  MODIFY `id_dado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id_endereco` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estados`
--
ALTER TABLE `estados`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id_funcionario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `manutencao`
--
ALTER TABLE `manutencao`
  MODIFY `id_manutencao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pecas`
--
ALTER TABLE `pecas`
  MODIFY `id_peca` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id_servico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `telefones`
--
ALTER TABLE `telefones`
  MODIFY `id_telefone` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id_venda` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `bairros`
--
ALTER TABLE `bairros`
  ADD CONSTRAINT `bairros_ibfk_1` FOREIGN KEY (`id_cidade`) REFERENCES `cidades` (`id_cidade`);

--
-- Restrições para tabelas `cicloprodutos`
--
ALTER TABLE `cicloprodutos`
  ADD CONSTRAINT `fk_produtos_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  ADD CONSTRAINT `fk_produtos_marca` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id_marca`);

--
-- Restrições para tabelas `cidades`
--
ALTER TABLE `cidades`
  ADD CONSTRAINT `cidades_ibfk_1` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`);

--
-- Restrições para tabelas `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`id_dado`) REFERENCES `dados_pessoais` (`id_dado`);

--
-- Restrições para tabelas `dados_pessoais`
--
ALTER TABLE `dados_pessoais`
  ADD CONSTRAINT `dados_pessoais_ibfk_1` FOREIGN KEY (`id_endereco`) REFERENCES `enderecos` (`id_endereco`);

--
-- Restrições para tabelas `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`id_bairro`) REFERENCES `bairros` (`id_bairro`);

--
-- Restrições para tabelas `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD CONSTRAINT `funcionarios_ibfk_1` FOREIGN KEY (`id_dado`) REFERENCES `dados_pessoais` (`id_dado`);

--
-- Restrições para tabelas `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD CONSTRAINT `itens_venda_ibfk_1` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id_venda`),
  ADD CONSTRAINT `itens_venda_ibfk_2` FOREIGN KEY (`id`) REFERENCES `cicloprodutos` (`id`);

--
-- Restrições para tabelas `manutencao`
--
ALTER TABLE `manutencao`
  ADD CONSTRAINT `manutencao_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Restrições para tabelas `servico_manutencao`
--
ALTER TABLE `servico_manutencao`
  ADD CONSTRAINT `fk_servico_manutencao_manutencao` FOREIGN KEY (`id_manutencao`) REFERENCES `manutencao` (`id_manutencao`),
  ADD CONSTRAINT `fk_servico_manutencao_servico` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id_servico`);

--
-- Restrições para tabelas `telefones`
--
ALTER TABLE `telefones`
  ADD CONSTRAINT `telefones_ibfk_1` FOREIGN KEY (`id_dado`) REFERENCES `dados_pessoais` (`id_dado`);

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
