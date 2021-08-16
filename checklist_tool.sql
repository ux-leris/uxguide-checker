-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16-Ago-2021 às 04:40
-- Versão do servidor: 10.4.14-MariaDB
-- versão do PHP: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `checklist_tool`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `access`
--

CREATE TABLE `access` (
  `user_id` int(255) NOT NULL,
  `checklist_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `checklist`
--

CREATE TABLE `checklist` (
  `id` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `author_id` int(255) NOT NULL,
  `published` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `checklist`
--

INSERT INTO `checklist` (`id`, `title`, `description`, `author_id`, `published`) VALUES
(67, 'Usability, User eXperience, and Accessibility Checklist for Deaf Assistive Technology (UUXAC-DAT)', 'É uma tecnologia de avaliação composta por itens de verificação, sendo dividido em 3 grupos (aspectos relacionados a percepção, a compreensão e operação).', 13, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `checklist_item`
--

CREATE TABLE `checklist_item` (
  `id` int(255) NOT NULL,
  `checklist_id` int(255) NOT NULL,
  `section_id` int(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `item_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `checklist_item`
--

INSERT INTO `checklist_item` (`id`, `checklist_id`, `section_id`, `text`, `link`, `item_order`) VALUES
(160, 67, 64, 'Os textos disponíveis no recurso de TA são legíveis?', NULL, 1),
(161, 67, 64, 'O recurso da TA apresenta legenda com tamanho legível?', NULL, 2),
(162, 67, 64, 'As notificações e feedback são emitidas em modo vibratório e visual?', NULL, 3),
(163, 67, 64, 'As imagens e vídeos estão de tamanhos e qualidades adequados, a fim de que os Surdos possam captar detalhes sobre os movimentos das mãos, olhos e boca?', NULL, 4),
(164, 67, 64, 'O recurso da TA apresenta cores contrastantes entre fonte e fundo?', NULL, 5),
(165, 67, 64, 'O recurso da TA é visualmente leve e simples?', NULL, 6),
(166, 67, 64, 'O layout, fontes e paleta de cores são padronizados?', NULL, 7),
(167, 67, 64, 'O recurso da TA apresenta consistência visual e textual em todas as telas de interação?', NULL, 8),
(168, 67, 64, 'As legendas apresentadas no recurso da TA estão na parte inferior da tela?', NULL, 9),
(169, 67, 64, 'O aplicativo bloqueia as interrupções durante a representação da língua de sinais?', NULL, 10),
(170, 67, 64, 'O recurso da TA mostra o progresso dos processos em andamento (como download)?', NULL, 11),
(171, 67, 65, 'Se um erro de entrada for automaticamente detectado, o item que apresenta o erro é identificado e detalhado para o Surdo?', NULL, 1),
(172, 67, 65, 'Os rótulos e instruções nos campos de entrada de dados são oferecidos na Língua de sinais?', NULL, 2),
(173, 67, 65, 'O recurso da TA considera a diversidade de acepções (diferentes significados de um mesmo termo) das palavras da Língua Portuguesa?', NULL, 3),
(174, 67, 65, 'O recurso da TA apresenta textos simples e curtos?', NULL, 4),
(175, 67, 65, 'O recurso da TA evita o uso de palavras estrangeiras, jargões e termos técnicos?', NULL, 5),
(176, 67, 65, 'O recurso da TA considera os regionalismos (como aipim e mandioca)?', NULL, 6),
(177, 67, 65, 'O recurso da TA permite que o Surdo controle a velocidade da interpretação da Língua de Sinais?', NULL, 7),
(178, 67, 65, 'O recurso da TA apresenta rótulo e instruções de texto associados aos campos de entradas de dados?', NULL, 8),
(179, 67, 65, 'Os significados das legendas correspondem ao das mensagens transmitidas oralmente?', NULL, 9),
(180, 67, 65, 'O recurso da TA apresenta na legenda informações sobre os ruídos e sons do ambiente? (como legendas descrevendo sons da natureza e do trânsito)', NULL, 10),
(181, 67, 65, 'O recurso da TA permite selecionar outra língua oral e outra língua de sinais? (função de alterar idioma)', NULL, 11),
(182, 67, 65, 'O recurso da TA apresenta texto e interpretação na Língua de sinais para qualquer conteúdo não textual (áudio e vídeo)?', NULL, 12),
(183, 67, 65, 'O recurso da TA é compatível com a maioria das tecnologias disponíveis? (permite integração com outras aplicações)', NULL, 13),
(184, 67, 65, 'Se o recurso da TA inclui um avatar para interpretar a Língua de sinais, seus movimentos são contínuos semelhantes ao do ser humano?', NULL, 14),
(185, 67, 65, 'O recurso da TA apresenta um tutorial de primeiros passos para o Surdo se familiarizar com a TA?', NULL, 15),
(186, 67, 65, 'O termo de uso do recurso da TA é apresentado em Português escrito e na Língua de sinais?', NULL, 16),
(187, 67, 65, 'O recurso da TA exibe as animações suavemente, permitindo seu acompanhamento?', NULL, 17),
(188, 67, 65, 'Se o recurso da TA inclui um avatar para interpretar a Libras, o avatar é 3D?', NULL, 18),
(189, 67, 65, 'Se o recurso da TA inclui um avatar para interpretar na Língua de sinais, ela permite ver o Avatar de corpo inteiro?', NULL, 19),
(190, 67, 65, 'Se o recurso da TA inclui um avatar para interpretar a Língua de sinais, o Avatar tem visual humanóide?', NULL, 20),
(191, 67, 65, 'Se o recurso da TA inclui um avatar para interpretar a Língua de sinais, ela permite que o usuário personalize o avatar?', NULL, 21),
(192, 67, 65, 'Ao retornarem à aplicação após um período sem usá-la, os Surdos conseguirão utilizá-la sem maior dificuldade?', NULL, 22),
(193, 67, 65, 'As instruções de uso estão disponíveis em texto, esquemas visuais e na Língua de sinais?', NULL, 23),
(194, 67, 66, 'Em aplicações que contabilizam o tempo (aplicações em que o tempo é cronometrado ou tem limite de tempo), o Surdo pode interagir na Língua de sinais?', NULL, 1),
(195, 67, 66, 'As interrupções (com alertas, atualizações de páginas) podem ser adiadas ou suprimidas pelos Surdos?', NULL, 2),
(196, 67, 66, 'Se uma sessão de autenticação expirar, o usuário poderá se autenticar novamente e continuar a atividade sem perder nenhum dado da página corrente?', NULL, 3),
(197, 67, 66, 'O recurso da TA considera a forma de manuseio do dispositivo móvel pelo Surdo, considerando que este pode precisar realizar uma sinalização durante a interação com a TA?', NULL, 4),
(198, 67, 66, 'O recurso da TA apresenta processos diretos e curtos para realizar as ações?', NULL, 5),
(199, 67, 66, 'O recurso da TA apresenta um fluxo de conteúdo em estruturas simples?', NULL, 6),
(200, 67, 66, 'No recurso da TA, o fechamento de anúncios é apresentado de forma clara e simples?', NULL, 7),
(201, 67, 66, 'O recurso da TA evita ações inesperadas (aplicativo fechar sem motivo aparente)?', NULL, 8),
(202, 67, 66, 'O recurso da TA possui perguntas realçando a necessidade de confirmação antes de realizar algumas ações de risco (botões: deletar, pagar e enviar)?', NULL, 9),
(203, 67, 66, 'Se o usuário deixou de preencher algum campo a TA evita zerar o formulário inteiro?', NULL, 10),
(204, 67, 66, 'O tempo de resposta da TA é satisfatório?', NULL, 11),
(205, 67, 66, 'O recurso da TA fornece a assistência necessária para que o Surdo consiga realizar as ações?', NULL, 12),
(206, 67, 66, 'Na sua opinião, o recurso da TA facilitará alguma atividade diária do surdo?', NULL, 13),
(207, 67, 66, 'Na sua opinião, a TA estimula a independência do Surdo?', NULL, 14),
(208, 67, 66, 'O sistema permite que o Surdo personalize a TA?', NULL, 15),
(209, 67, 66, 'O sistema permite que o Surdo personalize a TA?', NULL, 16),
(210, 67, 66, 'Os Surdos conseguirão registrar sua avaliação sobre a aplicação?', NULL, 17),
(211, 67, 66, 'Os Surdos conseguirão registrar sua avaliação sobre a aplicação?', NULL, 18);

-- --------------------------------------------------------

--
-- Estrutura da tabela `checklist_item_data`
--

CREATE TABLE `checklist_item_data` (
  `id` int(255) NOT NULL,
  `evaluation_id` int(255) NOT NULL,
  `checklist_item_id` int(255) NOT NULL,
  `label` int(255) DEFAULT NULL,
  `justification` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `checklist_item_data`
--

INSERT INTO `checklist_item_data` (`id`, `evaluation_id`, `checklist_item_id`, `label`, `justification`) VALUES
(735, 185, 160, NULL, NULL),
(736, 185, 161, NULL, NULL),
(737, 185, 162, NULL, NULL),
(738, 185, 163, NULL, NULL),
(739, 185, 164, NULL, NULL),
(740, 185, 165, NULL, NULL),
(741, 185, 166, NULL, NULL),
(742, 185, 167, NULL, NULL),
(743, 185, 168, NULL, NULL),
(744, 185, 169, NULL, NULL),
(745, 185, 170, NULL, NULL),
(746, 185, 171, NULL, NULL),
(747, 185, 172, NULL, NULL),
(748, 185, 173, NULL, NULL),
(749, 185, 174, NULL, NULL),
(750, 185, 175, NULL, NULL),
(751, 185, 176, NULL, NULL),
(752, 185, 177, NULL, NULL),
(753, 185, 178, NULL, NULL),
(754, 185, 179, NULL, NULL),
(755, 185, 180, NULL, NULL),
(756, 185, 181, NULL, NULL),
(757, 185, 182, NULL, NULL),
(758, 185, 183, NULL, NULL),
(759, 185, 184, NULL, NULL),
(760, 185, 185, NULL, NULL),
(761, 185, 186, NULL, NULL),
(762, 185, 187, NULL, NULL),
(763, 185, 188, NULL, NULL),
(764, 185, 189, NULL, NULL),
(765, 185, 190, NULL, NULL),
(766, 185, 191, NULL, NULL),
(767, 185, 192, NULL, NULL),
(768, 185, 193, NULL, NULL),
(769, 185, 194, NULL, NULL),
(770, 185, 195, NULL, NULL),
(771, 185, 196, NULL, NULL),
(772, 185, 197, NULL, NULL),
(773, 185, 198, NULL, NULL),
(774, 185, 199, NULL, NULL),
(775, 185, 200, NULL, NULL),
(776, 185, 201, NULL, NULL),
(777, 185, 202, NULL, NULL),
(778, 185, 203, NULL, NULL),
(779, 185, 204, NULL, NULL),
(780, 185, 205, NULL, NULL),
(781, 185, 206, NULL, NULL),
(782, 185, 207, NULL, NULL),
(783, 185, 208, NULL, NULL),
(784, 185, 209, NULL, NULL),
(785, 185, 210, NULL, NULL),
(786, 185, 211, NULL, NULL),
(787, 186, 160, NULL, NULL),
(788, 186, 161, NULL, NULL),
(789, 186, 162, NULL, NULL),
(790, 186, 163, NULL, NULL),
(791, 186, 164, NULL, NULL),
(792, 186, 165, NULL, NULL),
(793, 186, 166, NULL, NULL),
(794, 186, 167, NULL, NULL),
(795, 186, 168, NULL, NULL),
(796, 186, 169, NULL, NULL),
(797, 186, 170, NULL, NULL),
(798, 186, 171, NULL, NULL),
(799, 186, 172, NULL, NULL),
(800, 186, 173, NULL, NULL),
(801, 186, 174, NULL, NULL),
(802, 186, 175, NULL, NULL),
(803, 186, 176, NULL, NULL),
(804, 186, 177, NULL, NULL),
(805, 186, 178, NULL, NULL),
(806, 186, 179, NULL, NULL),
(807, 186, 180, NULL, NULL),
(808, 186, 181, NULL, NULL),
(809, 186, 182, NULL, NULL),
(810, 186, 183, NULL, NULL),
(811, 186, 184, NULL, NULL),
(812, 186, 185, NULL, NULL),
(813, 186, 186, NULL, NULL),
(814, 186, 187, NULL, NULL),
(815, 186, 188, NULL, NULL),
(816, 186, 189, NULL, NULL),
(817, 186, 190, NULL, NULL),
(818, 186, 191, NULL, NULL),
(819, 186, 192, NULL, NULL),
(820, 186, 193, NULL, NULL),
(821, 186, 194, NULL, NULL),
(822, 186, 195, NULL, NULL),
(823, 186, 196, NULL, NULL),
(824, 186, 197, NULL, NULL),
(825, 186, 198, NULL, NULL),
(826, 186, 199, NULL, NULL),
(827, 186, 200, NULL, NULL),
(828, 186, 201, NULL, NULL),
(829, 186, 202, NULL, NULL),
(830, 186, 203, NULL, NULL),
(831, 186, 204, NULL, NULL),
(832, 186, 205, NULL, NULL),
(833, 186, 206, NULL, NULL),
(834, 186, 207, NULL, NULL),
(835, 186, 208, NULL, NULL),
(836, 186, 209, NULL, NULL),
(837, 186, 210, NULL, NULL),
(838, 186, 211, NULL, NULL),
(839, 187, 160, NULL, NULL),
(840, 187, 161, NULL, NULL),
(841, 187, 162, NULL, NULL),
(842, 187, 163, NULL, NULL),
(843, 187, 164, NULL, NULL),
(844, 187, 165, NULL, NULL),
(845, 187, 166, NULL, NULL),
(846, 187, 167, NULL, NULL),
(847, 187, 168, NULL, NULL),
(848, 187, 169, NULL, NULL),
(849, 187, 170, NULL, NULL),
(850, 187, 171, NULL, NULL),
(851, 187, 172, NULL, NULL),
(852, 187, 173, NULL, NULL),
(853, 187, 174, NULL, NULL),
(854, 187, 175, NULL, NULL),
(855, 187, 176, NULL, NULL),
(856, 187, 177, NULL, NULL),
(857, 187, 178, NULL, NULL),
(858, 187, 179, NULL, NULL),
(859, 187, 180, NULL, NULL),
(860, 187, 181, NULL, NULL),
(861, 187, 182, NULL, NULL),
(862, 187, 183, NULL, NULL),
(863, 187, 184, NULL, NULL),
(864, 187, 185, NULL, NULL),
(865, 187, 186, NULL, NULL),
(866, 187, 187, NULL, NULL),
(867, 187, 188, NULL, NULL),
(868, 187, 189, NULL, NULL),
(869, 187, 190, NULL, NULL),
(870, 187, 191, NULL, NULL),
(871, 187, 192, NULL, NULL),
(872, 187, 193, NULL, NULL),
(873, 187, 194, NULL, NULL),
(874, 187, 195, NULL, NULL),
(875, 187, 196, NULL, NULL),
(876, 187, 197, NULL, NULL),
(877, 187, 198, NULL, NULL),
(878, 187, 199, NULL, NULL),
(879, 187, 200, NULL, NULL),
(880, 187, 201, NULL, NULL),
(881, 187, 202, NULL, NULL),
(882, 187, 203, NULL, NULL),
(883, 187, 204, NULL, NULL),
(884, 187, 205, NULL, NULL),
(885, 187, 206, NULL, NULL),
(886, 187, 207, NULL, NULL),
(887, 187, 208, NULL, NULL),
(888, 187, 209, NULL, NULL),
(889, 187, 210, NULL, NULL),
(890, 187, 211, NULL, NULL),
(891, 188, 160, NULL, NULL),
(892, 188, 161, NULL, NULL),
(893, 188, 162, NULL, NULL),
(894, 188, 163, NULL, NULL),
(895, 188, 164, NULL, NULL),
(896, 188, 165, NULL, NULL),
(897, 188, 166, NULL, NULL),
(898, 188, 167, NULL, NULL),
(899, 188, 168, NULL, NULL),
(900, 188, 169, NULL, NULL),
(901, 188, 170, NULL, NULL),
(902, 188, 171, NULL, NULL),
(903, 188, 172, NULL, NULL),
(904, 188, 173, NULL, NULL),
(905, 188, 174, NULL, NULL),
(906, 188, 175, NULL, NULL),
(907, 188, 176, NULL, NULL),
(908, 188, 177, NULL, NULL),
(909, 188, 178, NULL, NULL),
(910, 188, 179, NULL, NULL),
(911, 188, 180, NULL, NULL),
(912, 188, 181, NULL, NULL),
(913, 188, 182, NULL, NULL),
(914, 188, 183, NULL, NULL),
(915, 188, 184, NULL, NULL),
(916, 188, 185, NULL, NULL),
(917, 188, 186, NULL, NULL),
(918, 188, 187, NULL, NULL),
(919, 188, 188, NULL, NULL),
(920, 188, 189, NULL, NULL),
(921, 188, 190, NULL, NULL),
(922, 188, 191, NULL, NULL),
(923, 188, 192, NULL, NULL),
(924, 188, 193, NULL, NULL),
(925, 188, 194, NULL, NULL),
(926, 188, 195, NULL, NULL),
(927, 188, 196, NULL, NULL),
(928, 188, 197, NULL, NULL),
(929, 188, 198, NULL, NULL),
(930, 188, 199, NULL, NULL),
(931, 188, 200, NULL, NULL),
(932, 188, 201, NULL, NULL),
(933, 188, 202, NULL, NULL),
(934, 188, 203, NULL, NULL),
(935, 188, 204, NULL, NULL),
(936, 188, 205, NULL, NULL),
(937, 188, 206, NULL, NULL),
(938, 188, 207, NULL, NULL),
(939, 188, 208, NULL, NULL),
(940, 188, 209, NULL, NULL),
(941, 188, 210, NULL, NULL),
(942, 188, 211, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `evaluation`
--

CREATE TABLE `evaluation` (
  `id` int(255) NOT NULL,
  `checklist_id` int(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `author` int(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `time_elapsed` decimal(10,0) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `evaluation`
--

INSERT INTO `evaluation` (`id`, `checklist_id`, `date`, `author`, `status`, `time_elapsed`) VALUES
(185, 67, '2021-08-16 02:33:55', 13, 0, '100'),
(186, 67, '2021-08-16 02:35:08', 13, 0, '83'),
(187, 67, '2021-08-16 02:35:33', 13, 0, '30'),
(188, 67, '2021-08-16 02:35:58', 13, 0, '25');

-- --------------------------------------------------------

--
-- Estrutura da tabela `label`
--

CREATE TABLE `label` (
  `id` int(255) NOT NULL,
  `checklist_id` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `hasJustification` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `label`
--

INSERT INTO `label` (`id`, `checklist_id`, `title`, `hasJustification`) VALUES
(86, 67, 'Sim', 0),
(87, 67, 'Não', 1),
(88, 67, 'Se aplica parcialmente', 1),
(89, 67, 'Não se aplica', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `section`
--

CREATE TABLE `section` (
  `id` int(255) NOT NULL,
  `checklist_id` int(255) NOT NULL,
  `title` text NOT NULL,
  `position` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `section`
--

INSERT INTO `section` (`id`, `checklist_id`, `title`, `position`) VALUES
(64, 67, 'Grupo relacionado a percepção de componentes da interface gráfica e demais elementos presentes nas telas, tanto na execução de ações quanto na resposta da aplicação', 0),
(65, 67, 'Grupo relacionado a tudo o que se refere à TA de forma geral, quanto a identificação do objetivo, das ações disponíveis, da forma de manuseio, à retenção, e principalmente, às questões puramente cognitivas de compreensão, que envolvem as línguas e as linguagens', 1),
(66, 67, 'Grupo relacionado ao ponto de vista do que poder ser usado de maneira eficaz e eficiente', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
--

CREATE TABLE `user` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`) VALUES
(13, 'Gustavo Barbosa Santos', 'contato.gustavobarbosa@outlook.com', '7c67e713a4b4139702de1a4fac672344');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `access`
--
ALTER TABLE `access`
  ADD PRIMARY KEY (`user_id`,`checklist_id`);

--
-- Índices para tabela `checklist`
--
ALTER TABLE `checklist`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `checklist_item`
--
ALTER TABLE `checklist_item`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `checklist_item_data`
--
ALTER TABLE `checklist_item_data`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `label`
--
ALTER TABLE `label`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `checklist`
--
ALTER TABLE `checklist`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de tabela `checklist_item`
--
ALTER TABLE `checklist_item`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=212;

--
-- AUTO_INCREMENT de tabela `checklist_item_data`
--
ALTER TABLE `checklist_item_data`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=943;

--
-- AUTO_INCREMENT de tabela `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT de tabela `label`
--
ALTER TABLE `label`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de tabela `section`
--
ALTER TABLE `section`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de tabela `user`
--
ALTER TABLE `user`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
