-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/01/2026 às 12:46
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
-- Banco de dados: `nimbusdocs`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `portal_submission_files`
--

CREATE TABLE `portal_submission_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `submission_id` int(10) UNSIGNED NOT NULL,
  `document_type` enum('BALANCE_SHEET','DRE','POLICIES','CNPJ_CARD','POWER_OF_ATTORNEY','MINUTES','ARTICLES_OF_INCORPORATION','BYLAWS','OTHER') DEFAULT 'OTHER',
  `origin` enum('USER','ADMIN') NOT NULL DEFAULT 'USER',
  `visible_to_user` tinyint(1) NOT NULL DEFAULT 0,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `size_bytes` bigint(20) UNSIGNED NOT NULL,
  `storage_path` varchar(255) NOT NULL,
  `checksum` varchar(128) DEFAULT NULL,
  `current_version` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `portal_submission_files`
--

INSERT INTO `portal_submission_files` (`id`, `submission_id`, `document_type`, `origin`, `visible_to_user`, `original_name`, `stored_name`, `mime_type`, `size_bytes`, `storage_path`, `checksum`, `current_version`, `uploaded_at`) VALUES
(1, 1, 'OTHER', 'USER', 0, 'Relatorio_Mensal_Design Harmonia_25_11_24.pdf', '2e51ec9640c487ee3bc7e4af58a8c949.pdf', 'application/pdf', 9759293, '2025/11/2e51ec9640c487ee3bc7e4af58a8c949.pdf', 'b36ca6b8878395eaa0fa114bca568f7fe9a7a322696b92d8d61402173ba7e5a7', 1, '2025-11-26 21:16:01'),
(2, 2, 'OTHER', 'USER', 0, 'Relatorio_Mensal_Design Harmonia_25_11_24.pdf', 'a391f9f297f1a9f1afa553adb1cea5ac.pdf', 'application/pdf', 9759293, '2025/11/a391f9f297f1a9f1afa553adb1cea5ac.pdf', 'b36ca6b8878395eaa0fa114bca568f7fe9a7a322696b92d8d61402173ba7e5a7', 1, '2025-11-26 21:18:12'),
(3, 2, 'OTHER', 'ADMIN', 1, 'AGT-CRI-CHEMIN-II-DISPENSA-FUNDO-DE-JUROS_removed.pdf', 'ba090905d6c8a3ef0ffeeb29bba1f7f1.pdf', 'application/pdf', 280916, '2025/11/ba090905d6c8a3ef0ffeeb29bba1f7f1.pdf', 'b6b8f03ccd5ac4c33370edac3e3b6f90f493026126fd9b2739794f77ecab810f', 1, '2025-11-27 16:19:11'),
(4, 3, 'OTHER', 'USER', 0, 'CRI Conviva_1º Aditamento ao TS_v.final_27112025 (assinatura)-Assinado_removed.pdf', '753a3d2f1ca04e0322e76330708e257a.pdf', 'application/pdf', 920496, '2025/12/753a3d2f1ca04e0322e76330708e257a.pdf', 'e4ad4e58586a34fbf5d6ef4e9be011e1d6fa0a125be15461ae83a5f06a15168c', 1, '2025-12-01 18:59:30'),
(5, 4, 'OTHER', 'USER', 0, 'New Portable Document 1.pdf', '169b5d4cfae6d952655f431f31d64780.pdf', 'application/pdf', 40498, 'portal_uploads/2/169b5d4cfae6d952655f431f31d64780.pdf', '44e24611edb45ee6507b2053765bb9525cdf312282d378234dad96a90ccc66de', 1, '2025-12-02 19:44:08'),
(6, 4, 'OTHER', 'ADMIN', 1, 'New Portable Document 1.pdf', '61b7f4745d9e4388688c8433be369ed6.pdf', 'application/pdf', 40498, '2025/12/61b7f4745d9e4388688c8433be369ed6.pdf', '44e24611edb45ee6507b2053765bb9525cdf312282d378234dad96a90ccc66de', 1, '2025-12-02 19:44:32'),
(7, 5, 'BALANCE_SHEET', 'USER', 0, 'AGT-08.03.2024.pdf', '68a36a4da4c448995402da0900e549f7.pdf', 'application/pdf', 431720, 'portal_uploads/3/68a36a4da4c448995402da0900e549f7.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 17:54:09'),
(8, 5, 'DRE', 'USER', 0, 'AGT-08.03.2024.pdf', '1990aa8d2cd6dda0822c331c81ba52fc.pdf', 'application/pdf', 431720, 'portal_uploads/3/1990aa8d2cd6dda0822c331c81ba52fc.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 17:54:09'),
(9, 5, 'POLICIES', 'USER', 0, 'AGT-08.03.2024.pdf', '0805c2d754dc7c662e758730e040d973.pdf', 'application/pdf', 431720, 'portal_uploads/3/0805c2d754dc7c662e758730e040d973.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 17:54:09'),
(10, 5, 'CNPJ_CARD', 'USER', 0, 'AGT-08.03.2024.pdf', '62cda55d9894631e21d21bbaf2189e48.pdf', 'application/pdf', 431720, 'portal_uploads/3/62cda55d9894631e21d21bbaf2189e48.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 17:54:09'),
(11, 5, 'POWER_OF_ATTORNEY', 'USER', 0, 'AGT-08.03.2024.pdf', '2d9eb74906b1dd7a806f82285d91229d.pdf', 'application/pdf', 431720, 'portal_uploads/3/2d9eb74906b1dd7a806f82285d91229d.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 17:54:09'),
(12, 5, 'MINUTES', 'USER', 0, 'AGT-08.03.2024.pdf', '2cf78ff7cc5bf94ce976a97c7559cb81.pdf', 'application/pdf', 431720, 'portal_uploads/3/2cf78ff7cc5bf94ce976a97c7559cb81.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 17:54:09'),
(13, 5, 'ARTICLES_OF_INCORPORATION', 'USER', 0, 'AGT-08.03.2024.pdf', '2876fa5a4a85c5b88c16c93ef40b6dd3.pdf', 'application/pdf', 431720, 'portal_uploads/3/2876fa5a4a85c5b88c16c93ef40b6dd3.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 17:54:09'),
(14, 5, 'BYLAWS', 'USER', 0, 'AGT-08.03.2024.pdf', 'aa4b7a79a5cb656577dfcb32ad71e80a.pdf', 'application/pdf', 431720, 'portal_uploads/3/aa4b7a79a5cb656577dfcb32ad71e80a.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 17:54:09'),
(15, 6, 'BALANCE_SHEET', 'USER', 0, 'AGT-08.03.2024.pdf', '5ed3bcb0e2b3d78602424adbe391b6ba.pdf', 'application/pdf', 431720, 'portal_uploads/3/5ed3bcb0e2b3d78602424adbe391b6ba.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 18:20:29'),
(16, 6, 'DRE', 'USER', 0, 'AGT-08.03.2024.pdf', '8bec6679fadf570b403b117118ef5398.pdf', 'application/pdf', 431720, 'portal_uploads/3/8bec6679fadf570b403b117118ef5398.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 18:20:29'),
(17, 6, 'POLICIES', 'USER', 0, 'AGT-08.03.2024.pdf', '87ed95a1f842b55273b33f6baf63a0c7.pdf', 'application/pdf', 431720, 'portal_uploads/3/87ed95a1f842b55273b33f6baf63a0c7.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 18:20:29'),
(18, 6, 'CNPJ_CARD', 'USER', 0, 'AGT-08.03.2024.pdf', 'e46d7826b53a4aaa47eb357c6185e621.pdf', 'application/pdf', 431720, 'portal_uploads/3/e46d7826b53a4aaa47eb357c6185e621.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 18:20:29'),
(19, 6, 'POWER_OF_ATTORNEY', 'USER', 0, 'AGT-08.03.2024.pdf', 'a0e40d624bf0d17a792fb21343835856.pdf', 'application/pdf', 431720, 'portal_uploads/3/a0e40d624bf0d17a792fb21343835856.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 18:20:29'),
(20, 6, 'MINUTES', 'USER', 0, 'AGT-08.03.2024.pdf', '39aa5322f87f226703bc359e6e5f09dd.pdf', 'application/pdf', 431720, 'portal_uploads/3/39aa5322f87f226703bc359e6e5f09dd.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 18:20:29'),
(21, 6, 'ARTICLES_OF_INCORPORATION', 'USER', 0, 'AGT-08.03.2024.pdf', '8ee3a1227b0da82920130c689c0aca09.pdf', 'application/pdf', 431720, 'portal_uploads/3/8ee3a1227b0da82920130c689c0aca09.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 18:20:29'),
(22, 6, 'BYLAWS', 'USER', 0, 'AGT-08.03.2024.pdf', 'b76e55a8e227db5d2c965d251b28e88a.pdf', 'application/pdf', 431720, 'portal_uploads/3/b76e55a8e227db5d2c965d251b28e88a.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 18:20:29'),
(23, 6, 'OTHER', 'ADMIN', 1, 'AGT-08.03.2024.pdf', '17d5bb443d7fe33a185f7ba864435e3b.pdf', 'application/pdf', 431720, '2026/01\\17d5bb443d7fe33a185f7ba864435e3b.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 18:51:02');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `portal_submission_files`
--
ALTER TABLE `portal_submission_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `portal_submission_files`
--
ALTER TABLE `portal_submission_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `portal_submission_files`
--
ALTER TABLE `portal_submission_files`
  ADD CONSTRAINT `portal_submission_files_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `portal_submissions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
