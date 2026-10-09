-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-10-2026 a las 03:01:08
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ciclomanos`
--
CREATE DATABASE IF NOT EXISTS `ciclomanos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ciclomanos`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bairros`
--

DROP TABLE IF EXISTS `bairros`;
CREATE TABLE `bairros` (
  `id_bairro` int(11) NOT NULL,
  `nome_bairro` varchar(100) NOT NULL,
  `id_cidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bairros`
--

INSERT INTO `bairros` (`id_bairro`, `nome_bairro`, `id_cidade`) VALUES
(1, 'Residencial União', 1),
(2, 'Bosque dos Eucaliptos', 1),
(3, 'Jardim América', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(100) NOT NULL,
  `nome_categoria` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nome_categoria`) VALUES
(1, 'Mountain Bike'),
(2, 'Bicicletas'),
(3, 'Peças'),
(4, 'Acessórios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cicloprodutos`
--

DROP TABLE IF EXISTS `cicloprodutos`;
CREATE TABLE `cicloprodutos` (
  `id` int(10) NOT NULL,
  `produto` varchar(100) NOT NULL,
  `descricao` varchar(300) NOT NULL,
  `imagem` varchar(1000) NOT NULL,
  `id_marca` int(11) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `preco_venda` decimal(10,2) NOT NULL,
  `qtd_atual` int(100) NOT NULL,
  `estoque_minimo` int(100) NOT NULL,
  `id_categoria` int(100) NOT NULL,
  `modalidade` int(100) NOT NULL,
  `tamanho_aro` int(100) NOT NULL,
  `material` int(100) NOT NULL,
  `cor` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cicloprodutos`
--

INSERT INTO `cicloprodutos` (`id`, `produto`, `descricao`, `imagem`, `id_marca`, `modelo`, `preco_venda`, `qtd_atual`, `estoque_minimo`, `id_categoria`, `modalidade`, `tamanho_aro`, `material`, `cor`) VALUES
(1, 'Bicicleta Caloi Elite Carbon Sport', 'Bicicleta de alta performance aro 29, ideal para trilhas, passeios e competições, com quadro em carbono, suspensão dianteira e transmissão de 12 velocidades.', 'https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcTsaUPq1FtQzs8BHDddmgTPdNI3iGOH0FVfelcyzuNpEf5YJ_V--bh_5ZR3G_8-UiIZ6jY0i0qm5pZkwoh6usLUG7Go9OR4dH8arHCD-ukQ08GKAWWJF0c2w', 2, 'Elite Carbon Sport 2026', 999.99, 10, 2, 2, 0, 29, 0, 0),
(2, 'Bicicleta Aro 29 Aço Carbono Freios A Disco Suspensão 21', 'A bicicleta aro 29 em aço carbono da marca KGT é ideal para adultos que buscam um passeio confortável e seguro. Com freios a disco mecânico dianteiro e traseiro, proporciona uma frenagem eficiente em qualquer situação. Com 21 velocidades, permite ajustar a marcha de acordo com o terreno, garantindo', 'https://http2.mlstatic.com/D_NQ_NP_2X_872071-MLB91746760832_092025-F-bicicleta-aro-29-aco-carbono-freios-a-disco-suspenso-21-vel.webp', 3, 'KGT Bikes', 740.00, 6, 2, 2, 0, 29, 0, 0),
(6, 'BICICLETA AUDAX VENTUS 1000 CLARIS', 'Pronta para enfrentar terrenos ousados, a Bicicleta Speed Audax Ventus 1000 Claris oferece uma experiência de ciclismo inigualável, projetada com alta tecnologia para garantir leveza, versatilidade e precisão. Ideal para ciclistas que buscam desempenho extremo em diversas situações de movimento.', 'https://images.tcdn.com.br/img/img_prod/1372186/bicicleta_audax_ventus_1000_claris_azul_cyano_azul_escuro_501_variacao_1687_1_b2a837e3cc19fa2db4632d539210c5d4.jpg', 4, 'AUDAX VENTUS 1000 CLARIS', 999.99, 10, 2, 2, 0, 29, 0, 0),
(11, 'Aeroad CF SLX 7 Di2', 'Design de quadro mais rápido do pelotão (ou \"do ciclismo profissional\"). Tradição de corrida inigualável da Aeroad CFR. Grupo Shimano 105 Di2 com medidor de potência 4iiii. Rodas de carbono DT Swiss ARC 1600 de 65 mm. Tecnologia PACE: ajustes rápidos no cockpit.', 'https://dma.canyon.com/image/upload/w_2500,h_2500,c_fit/b_rgb:F2F2F2/f_auto/q_auto/v1777532962/2027_FULL_aeroad_cf-slx-7-di2_4531_R107_P01_zsqbop', 5, 'Aeroad CF SLX 7 Di2', 999.99, 6, 2, 2, 0, 29, 0, 0),
(14, 'Bicicleta Aro 700 Rino Speed Gaya Aluminio 2x9v', 'A bicicleta é elogiada por ser leve, bonita e adequada para iniciantes, com um bom custo-benefício. No entanto, há críticas sobre a qualidade do acabamento e algumas peças, como o pedal e o câmbio de marcha.', 'https://http2.mlstatic.com/D_NQ_NP_2X_988940-MLA110999033619_042026-F.webp', 6, 'Aro 700 Rino Speed Gaya Aluminio 2x9v', 999.99, 50, 10, 2, 0, 0, 0, 0),
(15, 'Bicicleta Aro 29 KRW Alumínio 27v Freio Hidráulico R7', 'Mountain Bike (MTB) equipada com o consagrado quadro First Smitt de tamanho 17,5 (tamanho M, ideal para ciclistas entre 1,60m e 1,72m de altura). Fabricada em alumínio 6061 leve e resistente, conta com cabeamento interno parcial para um visual moderno e limpo. Possui rodas aro 29 de parede dupla.', 'https://encrypted-tbn3.gstatic.com/shopping?q=tbn:ANd9GcSmtQApzly_MedvsbGL5ovHKN-cUbUFaKd4KNwg1dTk8aYBnmJ6vKflMB9R8GyzJyZdvx_59uPd27P-3ZM23vmt49juwIR4M8aXcJBTk3YxqCxEVZNRp9unnw', 7, 'Aro 29 KRW', 999.99, 2, 1, 2, 0, 0, 0, 0),
(16, 'Bicicleta Aro 26 Ultra Bikes Summer Bicolor 6 Marchas', 'A Bicicleta Ultra Bikes Summer é sinônimo de conforto, leveza e segurança, com toda a sua beleza no estilo vintage, ela é sua companheira ideal para as pedaladas diárias.', 'https://http2.mlstatic.com/D_NQ_NP_703742-MLA112553993240_062026-O.webp', 8, 'Aro 26 Ultra Bikes Summer', 726.00, 3, 1, 2, 0, 0, 0, 0),
(17, 'Bicicleta 29 GTSM1 I-Vtec Lite Shimano 21V Freio a Disco', 'Mountain Bike (MTB) equipada com o consagrado quadro First Smitt de tamanho 17,5 (tamanho M, ideal para ciclistas entre 1,60m e 1,72m de altura). Fabricada em alumínio 6061 leve e resistente, conta com cabeamento interno parcial para um visual moderno e limpo. Possui rodas aro 29 de parede dupla.', 'https://images.tcdn.com.br/img/img_prod/394779/bicicleta_29_gtsm1_i_vtec_lite_shimano_21v_freio_a_disco_5785_18391_2_20260608143505_a7109fbf5638.jpg', 9, '29 GTSM1 I-Vtec Lite Shimano 21V', 999.99, 1, 1, 2, 0, 0, 0, 0),
(18, 'Bicicleta Freeride GTS Aro 26 Freio Hidráulico 7 Marchas | Gtsm1 Freeride', 'Bicicleta GTSM1 FREERIDE NEW é ideal para quem quer começar no ciclismo Urbano, ideal para quem busca uma bike diferenciada e com design único e agressivo. O peso total da bike é de 15,2 kg montada. A Bike sai direto da Fábrica Oficial com mais de 30 anos no mercado e garantia exclusiva com suporte.', 'https://images.tcdn.com.br/img/img_prod/394779/bicicleta_freeride_gts_aro_26_freio_hidraulico_7_marchas_gtsm1_freeride_5125_variacao_16869_1_20ff60dac91d648a4b02fa97f46fac43_20260525112900.jpg', 9, 'Freeride GTS Aro 26 Freio Hidráulico 7 Marchas | Gtsm1 Freeride', 999.99, 1, 1, 2, 0, 0, 0, 0),
(19, 'Bicicleta Freeride GTS Aro 26 Freio Hidráulico 9 Marchas | Gtsm1 Freeride', 'Bicicleta GTSM1 FREERIDE NEW é ideal para quem quer começar no ciclismo Urbano, ideal para quem busca uma bike diferenciada e com design único e agressivo. O peso total da bike é de 15,2 kg montada. A Bike sai direto da Fábrica Oficial com mais de 30 anos no mercado e garantia exclusiva com suporte.', 'https://images.tcdn.com.br/img/img_prod/394779/bicicleta_freeride_gts_aro_26_freio_hidraulico_9_marchas_gtsm1_freeride_5085_variacao_18091_1_0f987a9d86f8fe43a6b2a44cad49dbc9_20260525112708.jpeg', 9, 'Freeride GTS Aro 26 Freio Hidráulico 9 Marchas | Gtsm1 Freeride', 999.99, 2, 1, 2, 0, 0, 0, 0),
(20, 'Trek Solstice Mips Bike Helmet', 'O Capacete de Ciclismo foi desenvolvido para ciclistas que buscam máximo conforto, ventilação e estilo em qualquer tipo de pedal. Com design aerodinâmico e acabamento sofisticado, oferece excelente desempenho aliado à segurança e leveza.', 'https://res.cloudinary.com/trekbikes/image/upload/f_auto,c_pad,ar_16:9,b_auto:border,w_680,q_auto/TrekSolsticeMipsHelmetCPSC-63896-F-Primary', 10, 'Helmet', 999.99, 3, 3, 4, 0, 29, 0, 0),
(21, 'Capacete Ciclismo Gta Inmold Start Led Mtb', 'Uso: Indicado para Mountain Bike (MTB) e pedaladas urbanas. Segurança: Inclui sinalizador traseiro com luzes LED para maior visibilidade em pedais noturnos. Conforto: Possui aberturas de ventilação, regulagem giratória e alças ajustáveis.', 'https://encrypted-tbn1.gstatic.com/shopping?q=tbn:ANd9GcSCkHpwOcVOBDe6R_f5CrVmj3jgUCLD_29alHCJJtPwgBzWdE571xaFXXOeVFxzm0eTO51xaAVEJMeXpd48LouzJyBpty-IEIWTaB9JrZlA61whQzFnOy1t8w', 11, 'Gta Inmold Start Led Mtb', 78.00, 3, 3, 4, 0, 29, 0, 0),
(22, 'Capacete de Ciclismo Giro Hale', 'Marca: Giro; Modelo: Hale; 22 entradas de ar para ventilação e sensação refrescante; Forro removível para a correta higienização; Sistema de ajuste roc loc sport; Fecho rápido com trava estilo catraca; Aba removível; Tecnologia InMold.', 'https://images.tcdn.com.br/img/img_prod/1501765/capacete_de_ciclismo_giro_hale_3879_1_119df872b3da3eae38696bb1be299fe9.jpeg', 12, 'Hale', 120.00, 3, 3, 4, 0, 29, 0, 0),
(23, 'Capacete de Ciclismo Giro Vasona', 'Marca: Giro; Modelo: Vasona; construção in-mold; roc loc 5 para conforto, estabilidade e leveza; acu dial para ajuste 360º, permitindo fixação do capacete à cabeça de forma mais segura e confortável.', 'https://images.tcdn.com.br/img/img_prod/1501765/capacete_de_ciclismo_giro_vasona_2141_1_979c7f461466802cfb3c5ea977302b42.jpg', 12, 'Giro', 429.00, 3, 3, 4, 0, 29, 0, 0),
(24, 'Capacete de Ciclismo Venom Rosa', 'O Capacete de Ciclismo Venom Rosa é a escolha ideal para ciclistas que buscam segurança, conforto e um visual moderno durante suas pedaladas. Com design aerodinâmico e pintura envernizada de alta qualidade, o modelo entrega excelente desempenho aliado a um estilo marcante.', 'https://images.tcdn.com.br/img/img_prod/1172121/capacete_de_ciclismo_venom_rosa_33_1_91efaabfaf7eeeabdc9a693cf6504408.jpg', 13, 'Venom', 169.00, 3, 3, 4, 0, 29, 0, 0),
(25, 'Capacete Ciclismo Vultro Razor Camaleão Azul', 'O Capacete de Ciclismo Vultro Razor Camaleão Azul é a escolha ideal para ciclistas que buscam conforto, segurança e um visual diferenciado durante as pedaladas. Com design aerodinâmico moderno e acabamento camaleão azul, oferece estilo único aliado à alta performance.', 'https://images.tcdn.com.br/img/img_prod/1172121/capacete_de_ciclismo_razor_camaleao_azul_113_1_60280871fb9d27d528bc014f195e39f0.jpg', 14, 'Vultro Razor Camaleão', 805.00, 3, 3, 4, 0, 29, 0, 0),
(26, 'Capacete Ciclismo Vultro Razor Vinho Preto', 'O Capacete de Ciclismo Vultro Razor Vinho e Preto foi desenvolvido para ciclistas que buscam segurança, conforto e excelente ventilação durante as pedaladas. Com design moderno e aerodinâmico, é ideal para MTB, ciclismo de estrada e pedal urbano. Sua construção In Mold em EPS com policarbonato.', 'https://images.tcdn.com.br/img/img_prod/1172121/capacete_de_ciclismo_razor_vinho_preto_111_1_89fc17a285153b21c7cc14ec4cabc810.jpg', 14, 'Vultro Razor', 805.00, 3, 3, 4, 0, 29, 0, 0),
(27, 'Capacete Ciclismo Vultro Razor Branco Perola', 'O Capacete de Ciclismo Vultro Razor Branco Pérola foi desenvolvido para ciclistas que buscam máximo conforto, ventilação e estilo em qualquer tipo de pedal. Com design aerodinâmico e acabamento sofisticado, oferece excelente desempenho aliado à segurança e leveza.', 'https://images.tcdn.com.br/img/img_prod/1172121/capacete_de_ciclismo_razor_branco_perola_107_1_f58e879b6238e3b340341ffe0e0af84f.jpg', 14, 'Vultro Razor', 805.00, 3, 3, 4, 0, 29, 0, 0),
(28, 'Capacete MTB SPACE III C/VIS. PT/TURQUESA', 'Segurança e Conforto. O acessório ideal para suas atividades. Este capacete oferece comodidade e proteção para que você aproveite ao máximo seus pedais, seja em passeios urbanos, treinos ou trilhas. Equipado com luz traseira integrada.', 'https://acdn-us.mitiendanube.com/stores/006/157/575/products/91caa2e464e31c0ec75a2b81825603b3-47616a1b9d63f2616117588482510946-1024-1024.webp', 15, 'e MTB SPACE III C/VIS', 79.00, 3, 3, 4, 0, 29, 0, 0),
(29, 'Capacete para Ciclismo MTB Alças Ajustáveis e 19 Entradas de Ar', 'O capacete Atrio MTB 2.0 é ideal para te proteger sempre que estiver andando de bicicleta. Possui 19 entradas para ventilação, acolchoamento interno removível e viseira removível. Muito versátil, e indicado para uso urbano ou MTB.', 'https://down-br.img.susercontent.com/file/sg-11134201-7rat4-mamt6v14ogsj21@resize_w450_nl.webp', 16, 'MTB', 114.00, 3, 3, 4, 0, 29, 0, 0),
(30, 'Capacete De Mountain Bike Para Adultos, Respirável Batfox Cáqui', 'Este capacete profissional para ciclismo apresenta um design moderno com textura camuflada em cáqui claro. Seu formato aerodinâmico e as grandes aberturas de ventilação com múltiplos canais combinam leveza, proteção robusta e excelente respirabilidade.', 'https://down-br.img.susercontent.com/file/sg-11134201-824hd-mpkl7r18p4pb4d@resize_w450_nl.webp', 17, 'Mountain Bike', 137.00, 3, 3, 4, 0, 29, 0, 0),
(31, 'Capacete Ciclismo Moove Mtb Speed Rosa', 'O Capacete MTB Moove é ideal para quem busca segurança e conforto durante o pedal. Desenvolvido para mountain bike e uso urbano. Com ajuste de tamanho 54 a 58, oferece encaixe confortável e firme, além de design moderno na cor rosa.', 'https://down-br.img.susercontent.com/file/br-11134207-820lb-mlco4jqkevpi95@resize_w450_nl.webp', 18, 'Moove Mtb Speed', 59.00, 3, 3, 4, 0, 29, 0, 0),
(32, 'Capacete Bike Ciclismo Rosa Leve', 'Capacete Bike DEKO Rosa. Desenvolvido para quem busca proteção sem abrir mão do estilo, ele possui design moderno e aerodinâmico, além de diversas entradas de ar que proporcionam excelente ventilação durante o uso. Sua estrutura é leve e resistente.', 'https://down-br.img.susercontent.com/file/br-11134207-820lc-mr223u95zshz67@resize_w450_nl.webp', 19, 'DEKO', 79.00, 3, 3, 4, 0, 29, 0, 0),
(33, 'Luva Ciclismo Absorção de Choque', 'Luvas que tornam as suas atividades mais agradaveis. O tecido de seda de gelo é macio e delicado, proteção solar externa, sensação interna. A absorção de umidade mantém a seco, usando orifícios de microfibra para exportar rapidamente o suor.', 'https://encrypted-tbn1.gstatic.com/shopping?q=tbn:ANd9GcRrDcAOS8gBsrt8HmgTLcjKjai-x6F8CUUbt0phjqRCS5Na_YAdhZrRFuwPCpVFm1eCVoF5TpxSFj1hWlUliEiN00e5tRC0FFenk5IPCuP4hHcoz-9VV0yYiA', 20, 'Touch Screedew', 198.00, 3, 3, 4, 0, 29, 0, 0),
(34, 'Luva Ciclismo HUPI Eco Dedo Curto Paintbrush', 'São confortáveis para provas longas e treinos, tem função de amortecer o impacto com o chão em quedas ou proteger as mãos de possíveis galhos soltos nas trilhas de bike.', 'https://static.hupishop.com.br/public/hupibikes/imagens/produtos/media/luva-hupi-eco-dedo-curto-paintbrush-7460.jpg', 21, 'HUPI Eco', 70.00, 3, 3, 4, 0, 29, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cidades`
--

DROP TABLE IF EXISTS `cidades`;
CREATE TABLE `cidades` (
  `id_cidade` int(11) NOT NULL,
  `nome_cidade` varchar(100) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cidades`
--

INSERT INTO `cidades` (`id_cidade`, `nome_cidade`, `id_estado`) VALUES
(1, 'São José dos Campos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `id_dado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `id_dado`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dados_pessoais`
--

DROP TABLE IF EXISTS `dados_pessoais`;
CREATE TABLE `dados_pessoais` (
  `id_dado` int(11) NOT NULL,
  `cpf` char(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `id_endereco` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dados_pessoais`
--

INSERT INTO `dados_pessoais` (`id_dado`, `cpf`, `nome`, `email`, `id_endereco`) VALUES
(1, '42701307848', 'Ana Luiza Silva Carmo Gouvêa', 'anadocx04@gmail.com', 1),
(2, '42703096879', 'Maria Luiza Barbosa Maciel', 'maria.eniac@ciclomanos.com', 2),
(3, '07343962604', 'Mirian Luiza Barbosa Maciel', 'mirianmlh2021@ciclomanos.com', 3),
(4, '26442364879', 'Claudinei Vieira Maciel', 'claudimaciel@ciclomanos.com', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enderecos`
--

DROP TABLE IF EXISTS `enderecos`;
CREATE TABLE `enderecos` (
  `id_endereco` int(11) NOT NULL,
  `rua` varchar(150) NOT NULL,
  `cep` char(8) NOT NULL,
  `id_bairro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `enderecos`
--

INSERT INTO `enderecos` (`id_endereco`, `rua`, `cep`, `id_bairro`) VALUES
(1, 'Rua Antônio Maximiano de Andrade', '12239005', 1),
(2, 'Rua Valinhos', '12233750', 2),
(3, 'Rua Valinhos', '12233750', 2),
(4, 'Rua Valinhos', '12233750', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

DROP TABLE IF EXISTS `estados`;
CREATE TABLE `estados` (
  `id_estado` int(11) NOT NULL,
  `nome_estado` varchar(50) NOT NULL,
  `uf` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id_estado`, `nome_estado`, `uf`) VALUES
(1, 'São Paulo', 'SP');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funcionarios`
--

DROP TABLE IF EXISTS `funcionarios`;
CREATE TABLE `funcionarios` (
  `id_funcionario` int(11) NOT NULL,
  `id_dado` int(11) NOT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `data_admissao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `funcionarios`
--

INSERT INTO `funcionarios` (`id_funcionario`, `id_dado`, `cargo`, `data_admissao`) VALUES
(1, 2, 'Atendente', '2026-07-01'),
(2, 3, 'Chefe de Oficina', '2020-01-01'),
(3, 4, 'Gerente', '2020-01-01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `itens_venda`
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
-- Estructura de tabla para la tabla `manutencao`
--

DROP TABLE IF EXISTS `manutencao`;
CREATE TABLE `manutencao` (
  `id_manutencao` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `data_entrada` date DEFAULT NULL,
  `entrega_estimada` date DEFAULT NULL,
  `status` enum('recebida','em_analise','em_manutencao','pronta') NOT NULL DEFAULT 'recebida'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL,
  `nome_marca` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id_marca`, `nome_marca`) VALUES
(1, 'Shimano'),
(2, 'Caloi'),
(3, 'KGT'),
(4, 'Audax'),
(5, 'Canyon'),
(6, 'Rino'),
(7, 'KRW'),
(8, 'Ultra Bikes'),
(9, 'GTSM1'),
(10, 'Trek'),
(11, 'GTA'),
(12, 'Giro'),
(13, 'Venom'),
(14, 'Vultro'),
(15, 'Space'),
(16, 'Atrio'),
(17, 'Batfox'),
(18, 'Moove'),
(19, 'DEKO'),
(20, 'Touch Screedew'),
(21, 'HUPI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ofertas`
--

DROP TABLE IF EXISTS `ofertas`;
CREATE TABLE `ofertas` (
  `id_oferta` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `preco_original` decimal(10,2) NOT NULL,
  `preco_oferta` decimal(10,2) NOT NULL,
  `percentual_desconto` decimal(5,2) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pecas`
--

DROP TABLE IF EXISTS `pecas`;
CREATE TABLE `pecas` (
  `id_peca` int(11) NOT NULL,
  `descricao` varchar(150) NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicos`
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
-- Estructura de tabla para la tabla `servico_manutencao`
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
-- Estructura de tabla para la tabla `telefones`
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
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_dado` int(11) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo` enum('admin','cliente','funcionario') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_dado`, `senha_hash`, `criado_em`, `tipo`) VALUES
(1, 1, '$2y$10$N3Ze.t5vHLx.MlrWZEwX5OFkLW9YM8T.iETflLBnSsWsQWl4bQ4FW', '2026-09-02 11:27:42', 'cliente'),
(2, 2, '$2y$10$XZYBbpecR1ibzWbioy9owOKcSU81i/84oeOCOgCcWVhL80fiF4JyC', '2026-10-09 00:36:57', 'funcionario'),
(3, 3, '$2y$10$j3L8tzde.Q2G7Er4YEU5mOhRzIMHvyypDtyi89JhYDll02S8hEkJe', '2026-10-09 00:43:09', 'funcionario'),
(4, 4, '$2y$10$qwY/Qzf6YU/9TjX5ygGKGupLXP6HJH9hRpR662eoaEAXkrxRUQzvK', '2026-10-09 00:51:40', 'funcionario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendas`
--

DROP TABLE IF EXISTS `vendas`;
CREATE TABLE `vendas` (
  `id_venda` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `data` date NOT NULL,
  `valor_total` decimal(10,2) DEFAULT 0.00,
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `status_pagamento` enum('pendente','aprovado','cancelado') DEFAULT 'pendente',
  `data_pagamento` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bairros`
--
ALTER TABLE `bairros`
  ADD PRIMARY KEY (`id_bairro`),
  ADD KEY `id_cidade` (`id_cidade`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `cicloprodutos`
--
ALTER TABLE `cicloprodutos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produtos_marca` (`id_marca`),
  ADD KEY `fk_cicloprodutos_categorias` (`id_categoria`);

--
-- Indices de la tabla `cidades`
--
ALTER TABLE `cidades`
  ADD PRIMARY KEY (`id_cidade`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `id_dado` (`id_dado`);

--
-- Indices de la tabla `dados_pessoais`
--
ALTER TABLE `dados_pessoais`
  ADD PRIMARY KEY (`id_dado`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `id_endereco` (`id_endereco`);

--
-- Indices de la tabla `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id_endereco`),
  ADD KEY `id_bairro` (`id_bairro`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id_funcionario`),
  ADD UNIQUE KEY `id_dado` (`id_dado`);

--
-- Indices de la tabla `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_venda` (`id_venda`),
  ADD KEY `itens_venda_ibfk_2` (`id`);

--
-- Indices de la tabla `manutencao`
--
ALTER TABLE `manutencao`
  ADD PRIMARY KEY (`id_manutencao`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id_marca`);

--
-- Indices de la tabla `ofertas`
--
ALTER TABLE `ofertas`
  ADD PRIMARY KEY (`id_oferta`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Indices de la tabla `pecas`
--
ALTER TABLE `pecas`
  ADD PRIMARY KEY (`id_peca`);

--
-- Indices de la tabla `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id_servico`);

--
-- Indices de la tabla `servico_manutencao`
--
ALTER TABLE `servico_manutencao`
  ADD PRIMARY KEY (`id_manutencao`,`id_servico`),
  ADD KEY `idx_servico_manutencao_servico` (`id_servico`);

--
-- Indices de la tabla `telefones`
--
ALTER TABLE `telefones`
  ADD PRIMARY KEY (`id_telefone`),
  ADD KEY `id_dado` (`id_dado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `id_dado` (`id_dado`);

--
-- Indices de la tabla `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id_venda`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bairros`
--
ALTER TABLE `bairros`
  MODIFY `id_bairro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cicloprodutos`
--
ALTER TABLE `cicloprodutos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `cidades`
--
ALTER TABLE `cidades`
  MODIFY `id_cidade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `dados_pessoais`
--
ALTER TABLE `dados_pessoais`
  MODIFY `id_dado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id_funcionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `itens_venda`
--
ALTER TABLE `itens_venda`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `manutencao`
--
ALTER TABLE `manutencao`
  MODIFY `id_manutencao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `ofertas`
--
ALTER TABLE `ofertas`
  MODIFY `id_oferta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pecas`
--
ALTER TABLE `pecas`
  MODIFY `id_peca` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id_servico` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `telefones`
--
ALTER TABLE `telefones`
  MODIFY `id_telefone` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id_venda` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bairros`
--
ALTER TABLE `bairros`
  ADD CONSTRAINT `bairros_ibfk_1` FOREIGN KEY (`id_cidade`) REFERENCES `cidades` (`id_cidade`);

--
-- Filtros para la tabla `cicloprodutos`
--
ALTER TABLE `cicloprodutos`
  ADD CONSTRAINT `fk_cicloprodutos_categorias` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produtos_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  ADD CONSTRAINT `fk_produtos_marca` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id_marca`);

--
-- Filtros para la tabla `cidades`
--
ALTER TABLE `cidades`
  ADD CONSTRAINT `cidades_ibfk_1` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`);

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`id_dado`) REFERENCES `dados_pessoais` (`id_dado`);

--
-- Filtros para la tabla `dados_pessoais`
--
ALTER TABLE `dados_pessoais`
  ADD CONSTRAINT `dados_pessoais_ibfk_1` FOREIGN KEY (`id_endereco`) REFERENCES `enderecos` (`id_endereco`);

--
-- Filtros para la tabla `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`id_bairro`) REFERENCES `bairros` (`id_bairro`);

--
-- Filtros para la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD CONSTRAINT `funcionarios_ibfk_1` FOREIGN KEY (`id_dado`) REFERENCES `dados_pessoais` (`id_dado`);

--
-- Filtros para la tabla `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD CONSTRAINT `itens_venda_ibfk_1` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id_venda`),
  ADD CONSTRAINT `itens_venda_ibfk_2` FOREIGN KEY (`id`) REFERENCES `cicloprodutos` (`id`);

--
-- Filtros para la tabla `manutencao`
--
ALTER TABLE `manutencao`
  ADD CONSTRAINT `manutencao_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Filtros para la tabla `ofertas`
--
ALTER TABLE `ofertas`
  ADD CONSTRAINT `ofertas_ibfk_1` FOREIGN KEY (`id_produto`) REFERENCES `cicloprodutos` (`id`);

--
-- Filtros para la tabla `servico_manutencao`
--
ALTER TABLE `servico_manutencao`
  ADD CONSTRAINT `fk_servico_manutencao_manutencao` FOREIGN KEY (`id_manutencao`) REFERENCES `manutencao` (`id_manutencao`),
  ADD CONSTRAINT `fk_servico_manutencao_servico` FOREIGN KEY (`id_servico`) REFERENCES `servicos` (`id_servico`);

--
-- Filtros para la tabla `telefones`
--
ALTER TABLE `telefones`
  ADD CONSTRAINT `telefones_ibfk_1` FOREIGN KEY (`id_dado`) REFERENCES `dados_pessoais` (`id_dado`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_dado`) REFERENCES `dados_pessoais` (`id_dado`);

--
-- Filtros para la tabla `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
