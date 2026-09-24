-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08/06/2026 às 12:33
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

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

-- --------------------------------------------------------

--
-- Estrutura para tabela `cicloprodutos`
--

CREATE TABLE `cicloprodutos` (
  `id` int(10) NOT NULL,
  `produto` varchar(100) NOT NULL,
  `descricao` varchar(300) NOT NULL,
  `imagem` varchar(1000) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `preco_venda` decimal(5,2) NOT NULL,
  `qtd_atual` int(100) NOT NULL,
  `estoque_minimo` int(100) NOT NULL,
  `categoria_principal` int(100) NOT NULL,
  `modalidade` int(100) NOT NULL,
  `tamanho_aro` int(100) NOT NULL,
  `material` int(100) NOT NULL,
  `cor` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cicloprodutos`
--

INSERT INTO `cicloprodutos` (`id`, `produto`, `descricao`, `imagem`, `marca`, `modelo`, `preco_venda`, `qtd_atual`, `estoque_minimo`, `categoria_principal`, `modalidade`, `tamanho_aro`, `material`, `cor`) VALUES
(1, 'Pneu Aro 24.', 'Pneu aro 24 resistente\r\nIdeal para bicicletas juvenis\r\nAlta durabilidade', 'tcc/aro24.jpg', '', '', 0.00, 0, 0, 0, 0, 0, 0, 0),
(3, 'Pneu Aro 26\r\n', 'Pneu aro 26 reforçado\r\nIdeal para MTB e uso urbano\r\nAlta resistência\r\nExcelente desempenho', 'tcc/aro26.jpg', '', '', 0.00, 0, 0, 0, 0, 0, 0, 0),
(4, 'Pneu Aro 29.', 'Pneu aro 29 premium\r\nIdeal para MTB e trilhas\r\nAlta performance\r\nExcelente tração', 'tcc/aro29.jpg', '', '', 0.00, 0, 0, 0, 0, 0, 0, 0),
(5, 'Pneu aro 20.', 'Pneu aro 20 resistente\r\nIdeal para bicicletas BMX e urbanas\r\nAlta durabilidade\r\nExcelente aderência em diversos terrenos', 'tcc/aro20.jpg', '', '', 0.00, 0, 0, 0, 0, 0, 0, 0),
(6, 'Pneu Aro 16.', 'Pneu aro 16 resistente\r\nIdeal para bicicletas infantis\r\nAlta durabilidade\r\nExcelente aderência', 'tcc/aro16.jpg', '', '', 0.00, 0, 0, 0, 0, 0, 0, 0),
(7, 'Rodas Aro 29.', '36 raios de aço preto\r\nCubo VZAN\r\nFreio 6 furos\r\nPeso 2.310g', 'https://www.flashbike.com.br/site/carrega?_tp=img5&_img=005938001.jpg', '', '', 0.00, 0, 0, 0, 0, 0, 0, 0),
(8, 'luvinha', 'luvinhas macias, a moda do momento, confortavel e linda', 'luva.jpg', 'GTA', 'grau', 30.00, 3, 1, 0, 0, 26, 0, 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cicloprodutos`
--
ALTER TABLE `cicloprodutos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cicloprodutos`
--
ALTER TABLE `cicloprodutos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
