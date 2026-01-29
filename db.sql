-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/01/2026 às 15:27
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
-- Estrutura para tabela `admin_audit_log`
--

CREATE TABLE `admin_audit_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `admin_user_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(190) NOT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `full_name` varchar(190) DEFAULT NULL,
  `azure_oid` varchar(64) DEFAULT NULL,
  `azure_tenant_id` varchar(64) DEFAULT NULL,
  `azure_upn` varchar(190) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `two_factor_secret` varchar(64) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `two_factor_confirmed_at` datetime DEFAULT NULL,
  `auth_mode` enum('LOCAL_ONLY','MS_ONLY','LOCAL_AND_MS') NOT NULL DEFAULT 'LOCAL_ONLY',
  `role` enum('SUPER_ADMIN','ADMIN','AUDITOR') NOT NULL DEFAULT 'ADMIN',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('ACTIVE','INACTIVE','BLOCKED') NOT NULL DEFAULT 'ACTIVE',
  `ms_object_id` varchar(190) DEFAULT NULL,
  `ms_tenant_id` varchar(190) DEFAULT NULL,
  `ms_upn` varchar(190) DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_provider` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `admin_users`
--

INSERT INTO `admin_users` (`id`, `name`, `email`, `full_name`, `azure_oid`, `azure_tenant_id`, `azure_upn`, `password_hash`, `two_factor_secret`, `two_factor_enabled`, `two_factor_confirmed_at`, `auth_mode`, `role`, `is_active`, `status`, `ms_object_id`, `ms_tenant_id`, `ms_upn`, `last_login_at`, `last_login_provider`, `created_at`, `updated_at`) VALUES
(1, 'Anderson Barbosa', 'anderson.cavalcante@bsicapital.com.br', 'Anderson Barbosa', NULL, NULL, NULL, '$2y$10$qb4OJVAHCI.Z09B0lTmf4ej98GD9jq5nOh2pdaYxCD.q.ZwAISZG2', 'UKT37CYBGSW4YP47', 0, NULL, 'LOCAL_ONLY', 'SUPER_ADMIN', 1, 'ACTIVE', NULL, NULL, NULL, '2026-01-29 12:26:17', 'LOCAL', '2025-11-12 14:03:45', '2026-01-29 15:26:17'),
(2, 'Teste', 'teste@bsicapital.com.br', 'Teste', NULL, NULL, NULL, '$2y$10$an4/xFwsFec7YL2BIttNm.4LdSPwwps.Q8E.l/iSXYJi9bSGE.H2G', NULL, 0, NULL, 'LOCAL_ONLY', 'ADMIN', 0, 'INACTIVE', NULL, NULL, NULL, NULL, NULL, '2025-12-02 19:21:17', '2026-01-28 13:49:33'),
(5, 'Administrador', 'admin@nimbusdocs.local', 'Administrador do Sistema', NULL, NULL, NULL, '$2y$10$NJLmdPmCRL0Yw2wnDeGWouiYCxIlXa.77nu/Id1vO4x2qbb9438AG', 'XIGHVC6NNL67PIFX', 0, NULL, 'LOCAL_ONLY', 'SUPER_ADMIN', 1, 'ACTIVE', NULL, NULL, NULL, '2026-01-07 11:55:02', 'LOCAL', '2026-01-05 18:53:04', '2026-01-07 14:59:51'),
(6, 'Teste', 'teste@email.com', 'Teste', NULL, NULL, NULL, '$2y$10$u8Y/5ZpPpe9e/sX2w27Bte/ot4GOPj4Z83ULZr92AcEkOnvAbRXn6', NULL, 0, NULL, 'LOCAL_ONLY', 'ADMIN', 0, 'INACTIVE', NULL, NULL, NULL, NULL, NULL, '2026-01-07 12:42:19', '2026-01-07 12:42:30'),
(7, 'Teste', 'teste@teste.com', 'Teste', NULL, NULL, NULL, '$2y$10$ObsqjrQVtv6tRhk4XXH3Eeh8kVc.ip6Y6f/xt/ZJ6c3k3ymiRb.Ia', NULL, 0, NULL, 'LOCAL_ONLY', 'ADMIN', 0, 'INACTIVE', NULL, NULL, NULL, NULL, NULL, '2026-01-28 13:44:41', '2026-01-28 13:45:03'),
(8, 'Teste II', 'teste@testando.com', 'Teste II', NULL, NULL, NULL, '$2y$10$RhQcwc8ySAICsF6Fvc4S8.wdl/9upgcqVhV2nU5Wp1y9Kb1NTgmvm', NULL, 0, NULL, 'LOCAL_ONLY', 'ADMIN', 0, 'INACTIVE', NULL, NULL, NULL, NULL, NULL, '2026-01-28 13:45:41', '2026-01-28 13:50:06'),
(9, 'Teste III', 'teste@teste.com.br', 'Teste III', NULL, NULL, NULL, '$2y$10$JDnJoIBJg.JAT2/P9hZareszXVF4aZYPGLZg0OgqxcD3z9PC7zsq6', NULL, 0, NULL, 'LOCAL_ONLY', 'ADMIN', 0, 'INACTIVE', NULL, NULL, NULL, NULL, NULL, '2026-01-28 13:50:36', '2026-01-28 13:50:54'),
(10, 'Teste IV', 'teste@testando.com.br', 'Teste IV', NULL, NULL, NULL, '$2y$10$rCbbtYkR0f/HsldvciOujO81titPtoeIsdQ/0EWpzUq00O4kEN3NG', NULL, 0, NULL, 'LOCAL_ONLY', 'ADMIN', 0, 'INACTIVE', NULL, NULL, NULL, NULL, NULL, '2026-01-28 13:51:23', '2026-01-28 13:51:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `api_tokens`
--

CREATE TABLE `api_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `admin_user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `last_4` varchar(4) NOT NULL,
  `scopes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`scopes`)),
  `last_used_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `app_settings`
--

CREATE TABLE `app_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(190) NOT NULL,
  `value` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `app_settings`
--

INSERT INTO `app_settings` (`id`, `key`, `value`, `updated_at`) VALUES
(1, 'app.name', 'NimbusDocs', '2026-01-28 15:49:48'),
(2, 'app.subtitle', 'Portal seguro de documentos', '2026-01-28 15:49:48'),
(3, 'branding.primary_color', '#00205b', '2026-01-28 15:49:48'),
(4, 'branding.accent_color', '#d6a100', '2026-01-28 15:49:48'),
(5, 'branding.admin_logo_url', '', '2026-01-28 15:49:48'),
(6, 'branding.portal_logo_url', '', '2026-01-28 15:49:48'),
(7, 'portal.notify.new_submission', '1', '2026-01-07 11:54:02'),
(8, 'portal.notify.status_change', '1', '2026-01-07 11:54:02'),
(9, 'portal.notify.response_upload', '1', '2026-01-07 11:54:02'),
(19, 'notifications.general_documents.enabled', '1', '2025-12-11 17:00:39'),
(20, 'notifications.announcements.enabled', '1', '2025-12-11 17:00:39'),
(21, 'notifications.submission_received.enabled', '1', '2025-12-11 17:00:39'),
(22, 'notifications.submission_status_changed.enabled', '1', '2025-12-11 17:00:39'),
(23, 'notifications.token_created.enabled', '1', '2025-12-11 17:00:39'),
(24, 'notifications.token_expired.enabled', '1', '2025-12-11 17:00:39'),
(25, 'notifications.user_precreated.enabled', '1', '2025-12-11 17:00:39'),
(42, 'portal.notify.access_link', '1', '2026-01-07 11:54:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `occurred_at` datetime NOT NULL DEFAULT current_timestamp(),
  `actor_type` enum('ADMIN','PORTAL_USER','SYSTEM') NOT NULL,
  `actor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `actor_name` varchar(190) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(100) DEFAULT NULL,
  `target_id` int(10) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `context_type` varchar(100) DEFAULT NULL,
  `context_id` bigint(20) UNSIGNED DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `occurred_at`, `actor_type`, `actor_id`, `actor_name`, `action`, `target_type`, `target_id`, `ip_address`, `user_agent`, `context_type`, `context_id`, `summary`, `details`) VALUES
(1, '2025-11-28 17:45:29', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, NULL),
(2, '2025-11-28 17:45:44', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2025-11-29 17:45:44.362543\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(3, '2025-12-01 10:13:21', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(4, '2025-12-01 15:48:46', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"code\":\"M2UST9TSH3H3\"}'),
(5, '2025-12-01 15:52:18', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, NULL),
(6, '2025-12-01 15:52:39', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2025-12-02 15:52:39.143725\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(7, '2025-12-01 15:55:54', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"code\":\"QS8656YBJVXX\"}'),
(8, '2025-12-01 15:55:59', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2025-12-02 15:55:59.155872\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(9, '2025-12-01 15:56:03', 'PORTAL_USER', 1, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(10, '2025-12-01 15:59:30', 'PORTAL_USER', 1, NULL, 'SUBMISSION_CREATED', 'PORTAL_SUBMISSION', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"reference_code\":\"SUB-20251201-F3XMXUP7\"}'),
(11, '2025-12-01 15:59:30', 'PORTAL_USER', 1, 'Teste', 'PORTAL_SUBMISSION_CREATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'submission', 3, 'Nova submissão criada pelo usuário do portal.', '{\"title\":\"Teste\",\"has_files\":true}'),
(12, '2025-12-01 16:57:33', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, NULL),
(13, '2025-12-02 10:03:09', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(14, '2025-12-02 14:23:08', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(15, '2025-12-02 16:26:38', 'ADMIN', 1, NULL, 'PORTAL_USER_CREATED', 'PORTAL_USER', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(16, '2025-12-02 16:29:05', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 2, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"lais_rodrigues@oi.com.br\",\"token_expires_at\":{\"date\":\"2025-12-03 16:29:05.663296\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(17, '2025-12-02 16:29:10', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 2, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"lais_rodrigues@oi.com.br\",\"token_expires_at\":{\"date\":\"2025-12-03 16:29:10.521596\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(18, '2025-12-02 16:32:10', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 2, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"lais_rodrigues@oi.com.br\",\"token_expires_at\":{\"date\":\"2025-12-03 16:32:10.468020\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(19, '2025-12-02 16:32:51', 'PORTAL_USER', 2, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, NULL),
(20, '2025-12-02 16:34:10', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, '{\"code\":\"4HZ8X4GFFYFZ\"}'),
(21, '2025-12-02 16:34:21', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, '{\"code\":\"CKBJ8DSBPMBA\"}'),
(22, '2025-12-02 16:34:29', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, '{\"code\":\"D4ZXQDET99RH\"}'),
(23, '2025-12-02 16:36:28', 'ADMIN', 1, NULL, 'PORTAL_USER_UPDATED', 'PORTAL_USER', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(24, '2025-12-02 16:42:44', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 2, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"lais_rodrigues@oi.com.br\",\"token_expires_at\":{\"date\":\"2025-12-03 16:42:44.433610\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(25, '2025-12-02 16:42:50', 'PORTAL_USER', 2, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 10, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, NULL),
(26, '2025-12-02 16:44:08', 'PORTAL_USER', 2, NULL, 'SUBMISSION_CREATED', 'PORTAL_SUBMISSION', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, '{\"reference_code\":\"SUB-20251202-EDEN4J3H\"}'),
(27, '2025-12-02 16:44:08', 'PORTAL_USER', 2, 'Laís Letícia Malu Rodrigues', 'PORTAL_SUBMISSION_CREATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', 'submission', 4, 'Nova submissão criada pelo usuário do portal.', '{\"title\":\"Teste\",\"has_files\":true}'),
(28, '2025-12-02 16:44:32', 'ADMIN', 1, 'Anderson Barbosa', 'SUBMISSION_RESPONSE_FILES_UPLOADED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'submission', 4, 'Arquivos de resposta enviados ao usuário.', '{\"files_count\":1}'),
(29, '2025-12-02 17:21:56', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 2, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"lais_rodrigues@oi.com.br\",\"token_expires_at\":{\"date\":\"2025-12-03 17:21:56.255649\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(30, '2025-12-02 17:22:47', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 2, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"lais_rodrigues@oi.com.br\",\"token_expires_at\":{\"date\":\"2025-12-03 17:22:47.112197\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(31, '2025-12-02 17:27:30', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 2, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"lais_rodrigues@oi.com.br\",\"token_expires_at\":{\"date\":\"2025-12-03 17:27:30.702240\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(32, '2025-12-02 17:27:39', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 2, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"lais_rodrigues@oi.com.br\",\"token_expires_at\":{\"date\":\"2025-12-03 17:27:39.249559\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(33, '2025-12-02 17:34:10', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2025-12-03 17:34:10.371643\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(34, '2025-12-02 17:35:25', 'PORTAL_USER', 1, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 15, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', NULL, NULL, NULL, NULL),
(35, '2025-12-03 09:57:50', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(36, '2025-12-03 09:58:10', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2025-12-04 09:58:10.017644\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(37, '2025-12-03 10:02:26', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2025-12-04 10:02:26.337489\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(38, '2025-12-03 10:03:56', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(39, '2025-12-03 10:04:08', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2025-12-04 10:04:08.823083\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(40, '2025-12-03 10:04:11', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2025-12-04 10:04:11.835772\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(41, '2025-12-03 10:04:31', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2025-12-04 10:04:31.133401\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(42, '2025-12-03 10:05:00', 'PORTAL_USER', 1, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 20, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(43, '2025-12-03 12:11:42', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(44, '2025-12-10 18:23:37', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL, NULL, NULL, NULL),
(45, '2025-12-10 18:24:29', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'portal_user', 2, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"lais_rodrigues@oi.com.br\",\"token_expires_at\":{\"date\":\"2025-12-11 18:24:29.286203\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(46, '2025-12-11 11:56:52', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(47, '2025-12-11 12:17:49', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(48, '2025-12-16 12:49:04', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(49, '2025-12-17 16:06:25', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(50, '2025-12-18 09:33:42', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(51, '2025-12-18 12:11:16', 'ADMIN', 1, NULL, 'PORTAL_USER_UPDATED', 'PORTAL_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(52, '2025-12-18 17:53:42', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(53, '2025-12-18 17:53:46', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(54, '2025-12-18 17:58:54', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(55, '2025-12-18 17:59:01', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(56, '2025-12-18 17:59:13', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(57, '2025-12-18 18:01:17', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(58, '2025-12-18 18:04:53', 'ADMIN', NULL, NULL, 'LOGIN_FAILED', 'ADMIN_USER', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(59, '2025-12-18 18:05:57', 'ADMIN', NULL, NULL, 'LOGIN_FAILED', 'ADMIN_USER', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(60, '2025-12-18 18:13:17', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(61, '2025-12-18 18:16:26', 'ADMIN', 1, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(62, '2025-12-18 18:20:25', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(63, '2025-12-18 18:21:57', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(64, '2025-12-18 18:22:40', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(65, '2025-12-18 18:23:27', 'ADMIN', NULL, NULL, 'LOGIN_FAILED', 'ADMIN_USER', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(66, '2025-12-18 18:24:50', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(67, '2025-12-18 18:25:35', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(68, '2025-12-18 18:25:44', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(69, '2025-12-18 18:25:49', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(70, '2025-12-18 18:26:01', 'ADMIN', NULL, NULL, 'LOGIN_FAILED', 'ADMIN_USER', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(71, '2025-12-18 18:31:51', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(72, '2025-12-18 18:48:58', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(73, '2025-12-18 18:50:50', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(74, '2025-12-18 18:52:26', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(75, '2025-12-18 18:52:43', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(76, '2025-12-18 18:52:54', 'ADMIN', 1, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(77, '2025-12-18 18:53:03', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(78, '2025-12-18 18:53:30', 'ADMIN', 1, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(79, '2025-12-18 18:53:36', 'ADMIN', NULL, NULL, 'LOGIN_FAILED', 'ADMIN_USER', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(80, '2025-12-18 18:53:57', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(81, '2025-12-18 18:57:10', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(82, '2025-12-18 18:57:43', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(83, '2025-12-18 18:58:43', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(84, '2025-12-18 19:02:56', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(85, '2025-12-18 19:04:38', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(86, '2025-12-18 19:05:17', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(87, '2025-12-18 19:06:20', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(88, '2025-12-18 19:08:23', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(89, '2025-12-18 19:08:27', 'ADMIN', 1, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(90, '2025-12-19 09:58:01', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(91, '2025-12-19 09:58:38', 'ADMIN', NULL, NULL, 'LOGIN_FAILED', 'ADMIN_USER', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(92, '2025-12-19 09:58:48', 'ADMIN', 1, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(93, '2025-12-19 09:59:09', 'ADMIN', 1, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(94, '2025-12-19 09:59:27', 'ADMIN', 1, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(95, '2025-12-19 09:59:31', 'ADMIN', 1, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(96, '2025-12-19 10:44:36', 'ADMIN', 1, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(97, '2026-01-05 15:41:18', 'ADMIN', 1, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(98, '2026-01-05 16:01:01', 'ADMIN', 5, NULL, 'LOGIN_FAILED', 'ADMIN_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(99, '2026-01-05 16:18:31', 'ADMIN', 5, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(100, '2026-01-06 09:54:56', 'ADMIN', 5, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(101, '2026-01-06 12:01:07', 'ADMIN', 5, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(102, '2026-01-06 12:01:18', 'ADMIN', 5, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(103, '2026-01-06 14:28:27', 'ADMIN', NULL, NULL, 'LOGIN_FAILED', 'ADMIN_USER', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL, NULL, NULL, NULL),
(104, '2026-01-06 14:28:44', 'ADMIN', NULL, NULL, 'LOGIN_FAILED', 'ADMIN_USER', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL, NULL, NULL, NULL),
(105, '2026-01-06 14:29:03', 'ADMIN', NULL, NULL, 'LOGIN_FAILED', 'ADMIN_USER', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL, NULL, NULL, NULL),
(106, '2026-01-06 14:29:21', 'ADMIN', NULL, NULL, 'LOGIN_FAILED', 'ADMIN_USER', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL, NULL, NULL, NULL),
(107, '2026-01-06 14:30:59', 'ADMIN', 5, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(108, '2026-01-06 14:43:32', 'ADMIN', 5, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL, NULL, NULL, NULL),
(109, '2026-01-06 16:18:34', 'ADMIN', 5, 'Administrador', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2026-01-07 16:18:34.172033\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(110, '2026-01-07 09:31:59', 'ADMIN', 5, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(111, '2026-01-07 11:24:07', 'ADMIN', 5, NULL, 'PORTAL_USER_UPDATED', 'PORTAL_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(112, '2026-01-07 11:24:52', 'ADMIN', 5, NULL, 'PORTAL_USER_CREATED', 'PORTAL_USER', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(113, '2026-01-07 11:53:48', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL, NULL, NULL, NULL),
(114, '2026-01-07 11:55:02', 'ADMIN', 5, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(115, '2026-01-07 12:00:42', 'ADMIN', 5, 'Administrador', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-08 12:00:42.144739\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(116, '2026-01-07 12:12:49', 'ADMIN', 5, 'Administrador', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2026-01-08 12:12:49.230596\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(117, '2026-01-07 12:28:50', 'ADMIN', 5, 'Administrador', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 2, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"lais_rodrigues@oi.com.br\",\"token_expires_at\":{\"date\":\"2026-01-07 13:28:50.297956\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(118, '2026-01-07 12:29:26', 'PORTAL_USER', 2, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 25, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL, NULL, NULL, NULL),
(119, '2026-01-07 12:38:47', 'ADMIN', 5, 'Administrador', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":{\"date\":\"2026-01-08 12:38:47.855278\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(120, '2026-01-07 12:43:27', 'PORTAL_USER', 1, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 26, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(121, '2026-01-07 12:43:46', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(122, '2026-01-07 14:45:48', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(123, '2026-01-07 14:46:06', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-08 14:46:06.663555\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(124, '2026-01-07 14:46:25', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 27, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(125, '2026-01-07 14:54:09', 'PORTAL_USER', 3, NULL, 'SUBMISSION_CREATED', 'PORTAL_SUBMISSION', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"reference_code\":\"SUB-20260107-K7X3N887\"}'),
(126, '2026-01-07 14:54:09', 'PORTAL_USER', 3, 'Benjamin Paulo Osvaldo Carvalho', 'PORTAL_SUBMISSION_CREATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'submission', 5, 'Nova submissão de cadastro criada.', '{\"company_name\":\"MICROSOFT 272945 BRASIL LTDA\",\"cnpj\":\"52.004.807\\/0003-68\"}'),
(127, '2026-01-07 15:20:29', 'PORTAL_USER', 3, NULL, 'SUBMISSION_CREATED', 'PORTAL_SUBMISSION', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"reference_code\":\"SUB-20260107-2DBYB6TN\"}'),
(128, '2026-01-07 15:20:29', 'PORTAL_USER', 3, 'Benjamin Paulo Osvaldo Carvalho', 'PORTAL_SUBMISSION_CREATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'submission', 6, 'Nova submissão de cadastro criada.', '{\"company_name\":\"MICROSOFT 272945 BRASIL LTDA\",\"cnpj\":\"52.004.807\\/0003-68\"}'),
(129, '2026-01-07 15:27:00', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-14 15:27:00.969945\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(130, '2026-01-07 15:27:28', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(131, '2026-01-07 15:50:44', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(132, '2026-01-07 15:51:02', 'ADMIN', 1, 'Anderson Barbosa', 'SUBMISSION_RESPONSE_FILES_UPLOADED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'submission', 6, 'Arquivos de resposta enviados ao usuário.', '{\"files_count\":1}'),
(133, '2026-01-07 15:51:19', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-08 15:51:19.813531\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(134, '2026-01-07 15:51:39', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 29, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(135, '2026-01-08 10:27:39', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(136, '2026-01-08 10:47:04', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(137, '2026-01-08 10:47:44', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-09 10:47:44.287758\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(138, '2026-01-08 10:47:50', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 30, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(139, '2026-01-08 11:38:19', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(140, '2026-01-08 11:38:33', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-09 11:38:33.440552\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(141, '2026-01-08 11:38:42', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 31, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(142, '2026-01-08 15:26:01', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(143, '2026-01-08 15:27:59', 'ADMIN', 1, NULL, 'PORTAL_USER_UPDATED', 'PORTAL_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(144, '2026-01-08 15:28:04', 'ADMIN', 1, NULL, 'PORTAL_USER_UPDATED', 'PORTAL_USER', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(145, '2026-01-08 15:28:08', 'ADMIN', 1, NULL, 'PORTAL_USER_UPDATED', 'PORTAL_USER', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(146, '2026-01-09 10:40:54', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(147, '2026-01-12 15:48:10', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(148, '2026-01-12 16:10:19', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-13 16:10:19.367194\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(149, '2026-01-12 16:10:27', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 32, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(150, '2026-01-12 16:31:16', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-13 16:31:16.266545\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(151, '2026-01-12 16:46:54', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 33, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(152, '2026-01-26 11:08:46', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(153, '2026-01-26 11:57:08', 'ADMIN', 1, NULL, 'SUBMISSION_STATUS_CHANGED', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"old_status\":\"COMPLETED\",\"new_status\":\"COMPLETED\",\"note\":\"\"}'),
(154, '2026-01-26 11:57:13', 'ADMIN', 1, NULL, 'SUBMISSION_STATUS_CHANGED', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"old_status\":\"COMPLETED\",\"new_status\":\"COMPLETED\",\"note\":\"\"}'),
(155, '2026-01-26 12:00:21', 'ADMIN', 1, NULL, 'SUBMISSION_STATUS_CHANGED', 'submission', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"old_status\":\"PENDING\",\"new_status\":\"REJECTED\",\"note\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vel placerat tortor, a molestie elit. Suspendisse in orci at risus pulvinar viverra in at purus. Suspendisse leo odio, condimentum vel risus ac, eleifend tincidunt lectus. Aenean finibus velit nec turpis vestibulum, eget congue sapien malesuada. Suspendisse feugiat dui quis nibh tempus, vel blandit erat tempor. In quis lacinia risus, non aliquam ipsum. Vestibulum quis nulla fermentum, convallis massa ut, dictum velit. Vestibulum consequat risus et quam mattis, id scelerisque nisi varius. Integer vestibulum, nibh et vehicula vehicula, eros massa accumsan odio, et rhoncus lacus massa ut diam. Aliquam quis elit at odio suscipit condimentum. Donec ultrices accumsan tristique. Vivamus sodales dictum magna ac consequat. Nunc sed justo tempus augue tempus vulputate vel pulvinar dui. Vivamus id lacus elit. Sed congue felis sed risus sollicitudin porta. Maecenas fermentum aliquet quam, in ultricies turpis sodales quis.\"}'),
(156, '2026-01-26 12:00:40', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-27 12:00:40.158506\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(157, '2026-01-26 12:00:45', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 34, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(158, '2026-01-26 12:17:13', 'ADMIN', 1, NULL, 'SUBMISSION_STATUS_CHANGED', 'submission', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"old_status\":\"PENDING\",\"new_status\":\"UNDER_REVIEW\",\"note\":\"Vestibulum iaculis dapibus sodales. Vestibulum sit amet nunc urna. Vivamus sagittis tristique nisi, convallis aliquet mauris semper at. Nullam non aliquet magna, vel placerat augue. Donec nec elit tortor. Nam justo lorem, placerat vel ipsum sit amet, consectetur volutpat mauris. Morbi molestie, nulla id varius ullamcorper, lectus urna dapibus arcu, vel aliquam turpis orci sed tellus. Suspendisse interdum mollis convallis. Cras arcu velit, mattis eget ullamcorper in, blandit tincidunt velit.\"}'),
(159, '2026-01-26 12:25:09', 'ADMIN', 1, NULL, 'SUBMISSION_STATUS_CHANGED', 'submission', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"old_status\":\"PENDING\",\"new_status\":\"COMPLETED\",\"note\":\"In mattis magna est, ac faucibus leo elementum et. Sed vestibulum consectetur magna eget vestibulum. Nulla mollis dui dolor, eu tincidunt leo tempus nec. Etiam pulvinar, dui nec iaculis iaculis, est dui vulputate massa, et interdum urna est ac ex. Sed lacinia tellus eget interdum tempor. Proin dignissim eros dapibus maximus ullamcorper. Curabitur dui lacus, commodo ut lacus in, rutrum aliquam mi. Nulla in dui ut neque pulvinar dapibus placerat vitae massa. Nullam eget justo risus. Fusce odio ligula, iaculis sit amet vehicula non, malesuada vitae sapien. Cras gravida elementum finibus. Aenean sit amet venenatis neque.\"}');
INSERT INTO `audit_logs` (`id`, `occurred_at`, `actor_type`, `actor_id`, `actor_name`, `action`, `target_type`, `target_id`, `ip_address`, `user_agent`, `context_type`, `context_id`, `summary`, `details`) VALUES
(160, '2026-01-26 12:25:45', 'ADMIN', 1, NULL, 'SUBMISSION_STATUS_CHANGED', 'submission', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"old_status\":\"PENDING\",\"new_status\":\"REJECTED\",\"note\":\"Aenean consequat euismod metus, sed sollicitudin nibh facilisis vitae. Nam non lacus mauris. Cras vel purus vulputate, laoreet dolor et, condimentum diam. Vestibulum eu metus convallis, tincidunt urna ac, pellentesque ipsum. Nam aliquam bibendum velit, et imperdiet ipsum mattis eu. Ut odio ligula, gravida eu dolor id, convallis dapibus turpis. Duis augue neque, placerat et mattis sit amet, egestas ac nisi. Praesent in felis nec velit elementum bibendum. In et sagittis mi. Suspendisse vel eleifend nulla. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Maecenas eu placerat augue. Fusce tempor laoreet posuere. Nulla ante dui, sollicitudin at purus et, semper commodo velit. Cras dignissim imperdiet sem, in tempus ex hendrerit at.\"}'),
(161, '2026-01-26 12:55:23', 'PORTAL_USER', 3, NULL, 'SUBMISSION_CREATED', 'PORTAL_SUBMISSION', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"reference_code\":\"SUB-20260126-ZQYBMBSN\"}'),
(162, '2026-01-26 12:55:23', 'PORTAL_USER', 3, 'Benjamin Paulo Osvaldo Carvalho', 'PORTAL_SUBMISSION_CREATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'submission', 7, 'Nova submissão de cadastro criada.', '{\"company_name\":\"MICROSOFT 272945 BRASIL LTDA\",\"cnpj\":\"52.004.807\\/0003-68\"}'),
(163, '2026-01-26 12:55:33', 'ADMIN', 1, NULL, 'FILE_PREVIEW', 'PORTAL_SUBMISSION_FILE', 24, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(164, '2026-01-26 12:57:26', 'ADMIN', 1, NULL, 'FILE_DOWNLOAD', 'PORTAL_SUBMISSION_FILE', 24, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(165, '2026-01-26 13:00:09', 'ADMIN', 1, NULL, 'SUBMISSION_STATUS_CHANGED', 'submission', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"old_status\":\"PENDING\",\"new_status\":\"REJECTED\",\"note\":\"Nam nec odio ut arcu porta elementum. Aliquam tincidunt nulla at erat placerat, ac egestas lectus aliquam. Morbi nec quam pharetra, ultricies purus id, mattis ante. Aenean id ex diam. Integer euismod pharetra ante condimentum malesuada. Aenean vel ipsum sed ipsum fermentum molestie a eget sem. Aenean ornare libero enim, eget convallis augue rutrum nec. Etiam eu elementum lacus.\"}'),
(166, '2026-01-26 13:05:30', 'PORTAL_USER', 3, NULL, 'SUBMISSION_CREATED', 'PORTAL_SUBMISSION', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"reference_code\":\"SUB-20260126-DTFBGNU9\"}'),
(167, '2026-01-26 13:05:30', 'PORTAL_USER', 3, 'Benjamin Paulo Osvaldo Carvalho', 'PORTAL_SUBMISSION_CREATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'submission', 8, 'Nova submissão de cadastro criada.', '{\"company_name\":\"MICROSOFT DO BRASIL IMPORTACAO E COMERCIO DE SOFTWARE E VIDEO GAMES LTDA\",\"cnpj\":\"04.712.500\\/0001-07\"}'),
(168, '2026-01-26 13:05:50', 'ADMIN', 1, NULL, 'FILE_PREVIEW', 'PORTAL_SUBMISSION_FILE', 24, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(169, '2026-01-26 13:06:25', 'ADMIN', 1, NULL, 'FILE_PREVIEW', 'PORTAL_SUBMISSION_FILE', 32, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(170, '2026-01-27 10:19:15', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(171, '2026-01-27 10:19:25', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-28 10:19:25.432342\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(172, '2026-01-27 10:19:28', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 35, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(173, '2026-01-27 12:06:05', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-28 12:06:05.613909\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(174, '2026-01-27 12:06:16', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 36, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(175, '2026-01-27 12:16:47', 'ADMIN', 1, NULL, 'FILE_PREVIEW', 'PORTAL_SUBMISSION_FILE', 32, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(176, '2026-01-27 17:21:17', 'ADMIN', 1, NULL, 'FILE_PREVIEW', 'PORTAL_SUBMISSION_FILE', 32, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(177, '2026-01-28 10:22:08', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(178, '2026-01-28 10:34:19', 'ADMIN', 1, NULL, 'SUBMISSION_STATUS_CHANGED', 'submission', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"old_status\":\"PENDING\",\"new_status\":\"COMPLETED\",\"note\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisi neque, varius non velit eget, iaculis rutrum urna. Fusce dignissim hendrerit viverra. Morbi tincidunt ac leo in tristique. Ut aliquam ante odio. Nunc tortor enim, blandit et massa blandit, semper euismod quam. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer rhoncus tortor a tempor molestie. Suspendisse vel neque est. Cras neque quam, faucibus eu cursus a, sodales sed enim. Cras vestibulum, tortor elementum faucibus pellentesque, metus elit maximus nibh, ac laoreet nisi mauris gravida ipsum. Proin in lorem id tortor auctor sagittis. In faucibus nulla in urna ultricies maximus. Morbi tincidunt rhoncus lectus, consectetur cursus odio pellentesque vel. Suspendisse efficitur, purus in pretium volutpat, ante lacus cursus enim, ac mattis sem neque mollis purus. Pellentesque hendrerit dui eu feugiat suscipit.\"}'),
(179, '2026-01-28 11:00:38', 'ADMIN', 1, NULL, 'PORTAL_USER_CREATED', 'PORTAL_USER', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(180, '2026-01-28 11:01:15', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'portal_user', 4, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"luizdanilopereira@velc.com.br\",\"token_expires_at\":{\"date\":\"2026-01-29 11:01:15.136034\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(181, '2026-01-28 11:01:25', 'PORTAL_USER', 4, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 37, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(182, '2026-01-28 14:47:43', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(183, '2026-01-28 14:48:39', 'ADMIN', 1, NULL, 'PORTAL_USER_CREATED', 'PORTAL_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(184, '2026-01-28 14:48:47', 'ADMIN', 1, NULL, 'PORTAL_USER_UPDATED', 'PORTAL_USER', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(185, '2026-01-28 14:56:01', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'portal_user', 5, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"andersoncavalcantr96@hotmail.com\",\"token_expires_at\":{\"date\":\"2026-01-29 14:56:01.135417\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(186, '2026-01-28 16:16:40', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'portal_user', 3, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"token_expires_at\":{\"date\":\"2026-01-29 16:16:40.791424\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(187, '2026-01-28 16:34:32', 'PORTAL_USER', 3, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 39, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(188, '2026-01-28 16:44:16', 'PORTAL_USER', 3, NULL, 'SUBMISSION_CREATED', 'PORTAL_SUBMISSION', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"reference_code\":\"SUB-20260128-UGQWB2VD\"}'),
(189, '2026-01-28 16:44:16', 'PORTAL_USER', 3, 'Benjamin Paulo Osvaldo Carvalho', 'PORTAL_SUBMISSION_CREATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'submission', 9, 'Nova submissão de cadastro criada.', '{\"company_name\":\"RED BULL DO BRASIL LTDA.\",\"cnpj\":\"02.946.761\\/0001-66\"}'),
(190, '2026-01-28 16:44:45', 'ADMIN', 1, NULL, 'FILE_PREVIEW', 'PORTAL_SUBMISSION_FILE', 40, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(191, '2026-01-28 16:45:35', 'ADMIN', 1, NULL, 'SUBMISSION_STATUS_CHANGED', 'submission', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"old_status\":\"PENDING\",\"new_status\":\"COMPLETED\",\"note\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam mollis, felis rutrum finibus hendrerit, ipsum orci ullamcorper mauris, id lobortis justo leo eget velit. Nullam vel dui justo. Nulla ornare rutrum eros eget ornare. Ut at dui justo. Pellentesque efficitur nisi et neque luctus egestas. Etiam eu justo in turpis rhoncus feugiat vitae et tortor. Aliquam purus nulla, consequat pharetra placerat ut, vulputate et elit. Nam sodales justo et nibh rutrum, id sollicitudin nibh ullamcorper. Nam suscipit ligula et ultrices mattis. Cras blandit nisi sit amet est facilisis egestas. Donec turpis nisl, fermentum quis mi id, malesuada pulvinar dui. Ut porttitor, dolor at vulputate pretium, mauris odio pulvinar sem, vel luctus tellus nunc sed nibh. Pellentesque posuere eget ex et condimentum. Vivamus porta, augue ut pellentesque mattis, elit ex efficitur enim, quis egestas tellus odio ut neque. Integer commodo orci magna, at mollis est mollis nec. Quisque massa risus, maximus id fringilla vitae, bibendum sed neque.\"}'),
(192, '2026-01-28 17:18:02', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', NULL, NULL, NULL, NULL),
(193, '2026-01-28 17:18:49', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'portal_user', 5, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"andersoncavalcantr96@hotmail.com\",\"token_expires_at\":{\"date\":\"2026-01-29 17:18:49.498454\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(194, '2026-01-28 17:19:26', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', NULL, NULL, NULL, '{\"code\":\"YWFT-G4BX-2FRZ\"}'),
(195, '2026-01-28 17:19:29', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'portal_user', 5, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"andersoncavalcantr96@hotmail.com\",\"token_expires_at\":{\"date\":\"2026-01-29 17:19:29.234192\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(196, '2026-01-28 17:19:34', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', NULL, NULL, NULL, '{\"code\":\"KYJW-TBA6-BDBV\"}'),
(197, '2026-01-28 17:19:38', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'portal_user', 5, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"andersoncavalcantr96@hotmail.com\",\"token_expires_at\":{\"date\":\"2026-01-29 17:19:38.949342\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(198, '2026-01-28 17:19:44', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', NULL, NULL, NULL, '{\"code\":\"DA7A-JP8E-MKMK\"}'),
(199, '2026-01-28 17:20:45', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', NULL, NULL, NULL, '{\"code\":\"DA7A-JP8E-MKMK\"}'),
(200, '2026-01-28 17:20:52', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'portal_user', 5, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"andersoncavalcantr96@hotmail.com\",\"token_expires_at\":{\"date\":\"2026-01-29 17:20:52.193484\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(201, '2026-01-28 17:20:58', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', NULL, NULL, NULL, '{\"code\":\"JMC2-2FC6-8EZJ\"}'),
(202, '2026-01-28 17:21:44', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'portal_user', 5, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"andersoncavalcantr96@hotmail.com\",\"token_expires_at\":{\"date\":\"2026-01-29 17:21:44.581251\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(203, '2026-01-28 17:21:50', 'PORTAL_USER', 5, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 44, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', NULL, NULL, NULL, NULL),
(204, '2026-01-29 09:32:27', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(205, '2026-01-29 09:32:52', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'portal_user', 5, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"andersoncavalcantr96@hotmail.com\",\"token_expires_at\":{\"date\":\"2026-01-30 09:32:52.109471\",\"timezone_type\":3,\"timezone\":\"America\\/Sao_Paulo\"}}'),
(206, '2026-01-29 09:32:56', 'PORTAL_USER', 5, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 45, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(207, '2026-01-29 12:26:17', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL),
(208, '2026-01-29 12:26:23', 'ADMIN', 1, 'Anderson Barbosa', 'PORTAL_ACCESS_LINK_GENERATED', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'portal_user', 1, 'Link de acesso único gerado para usuário do portal.', '{\"portal_user_email\":\"teste@teste.com\",\"token_expires_at\":\"[REDACTED]\"}'),
(209, '2026-01-29 12:26:30', 'PORTAL_USER', NULL, NULL, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, '{\"code\":\"UBRU2BT42UC7\"}'),
(210, '2026-01-29 12:26:33', 'PORTAL_USER', 1, NULL, 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', 46, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `auth_rate_limits`
--

CREATE TABLE `auth_rate_limits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `scope` varchar(40) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `attempts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `blocked_until` datetime DEFAULT NULL,
  `last_attempt_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `document_categories`
--

CREATE TABLE `document_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `document_categories`
--

INSERT INTO `document_categories` (`id`, `name`, `description`, `sort_order`, `created_at`) VALUES
(4, 'Teste', 'sdkgvlksdnvblsdnbvlsdnvbsd\\\\sdb', 1, '2026-01-07 10:36:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `general_documents`
--

CREATE TABLE `general_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_mime` varchar(255) NOT NULL,
  `file_size` bigint(20) UNSIGNED NOT NULL,
  `file_original_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `published_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by_admin` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `general_documents`
--

INSERT INTO `general_documents` (`id`, `category_id`, `title`, `description`, `file_path`, `file_mime`, `file_size`, `file_original_name`, `is_active`, `published_at`, `created_by_admin`, `created_at`) VALUES
(4, 4, 'Teste', 'erdfhbderhbhbxhshjdfh', 'C:\\xampp\\htdocs\\NimbusDocs\\src\\Presentation\\Controller\\Admin/../../../../storage/general_documents/\\405d774562b75eb8b88223d9ae4a7e21.pdf', 'application/pdf', 431720, 'AGT-08.03.2024.pdf', 1, '2026-01-07 10:38:06', 5, '2026-01-07 10:38:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `migrations`
--

INSERT INTO `migrations` (`id`, `filename`, `executed_at`) VALUES
(1, '20250101000000_create_core_tables.sql', '2025-12-03 13:09:29'),
(2, '20251202000100_add_auditor_role_to_admin_users.sql', '2025-12-03 13:09:29'),
(3, '20251202000200_update_portal_submissions_structure.sql', '2025-12-03 14:16:49'),
(4, '20251203000300_create_portal_announcements.sql', '2025-12-03 14:16:49'),
(5, 'fix_audit_logs.sql', '2025-12-03 14:16:49'),
(6, '20251211000400_create_app_settings.sql', '2025-12-17 19:10:55'),
(7, '20251217000500_create_notification_outbox.sql', '2025-12-17 19:10:55'),
(8, '20251217000600_alter_notification_outbox_status.sql', '2025-12-17 19:43:52'),
(9, '20251218001000_create_password_reset_tokens.sql', '2025-12-18 20:56:56'),
(10, '20251218001100_add_2fa_to_admin_users.sql', '2025-12-18 20:56:56'),
(11, '20251218001200_create_admin_notifications.sql', '2025-12-18 20:56:56'),
(12, '20251218001300_create_file_versions.sql', '2025-12-18 20:56:56'),
(13, '20251218001400_create_api_tokens.sql', '2025-12-18 20:56:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notification_log`
--

CREATE TABLE `notification_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `outbox_id` bigint(20) UNSIGNED NOT NULL,
  `event` enum('ATTEMPT','SENT','FAILED') NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notification_outbox`
--

CREATE TABLE `notification_outbox` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(80) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `template` varchar(120) NOT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload_json`)),
  `status` enum('PENDING','SENDING','SENT','FAILED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `max_attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `next_attempt_at` datetime DEFAULT NULL,
  `last_error` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notification_outbox`
--

INSERT INTO `notification_outbox` (`id`, `type`, `recipient_email`, `recipient_name`, `subject`, `template`, `payload_json`, `status`, `attempts`, `max_attempts`, `next_attempt_at`, `last_error`, `created_at`, `sent_at`) VALUES
(1, 'TOKEN_CREATED', 'teste@teste.com', 'Teste', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":1,\"full_name\":\"Teste\",\"email\":\"teste@teste.com\",\"document_number\":\"71044965053\",\"phone_number\":\"11999999999\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2025-12-03 10:05:00\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2025-11-26 18:14:40\",\"updated_at\":\"2025-12-18 12:11:16\"},\"token\":{\"id\":22,\"portal_user_id\":1,\"code\":\"EGTAQM7TH5EV\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-07 16:18:34\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-06 16:18:34\",\"user_name\":\"Teste\",\"user_email\":\"teste@teste.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-06 16:18:34', '2026-01-28 12:11:43'),
(2, 'USER_PRECREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Acesso ao portal NimbusDocs', 'user_precreated', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":null},\"token\":null}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 11:24:52', '2026-01-28 12:11:43'),
(3, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":null},\"token\":{\"id\":23,\"portal_user_id\":3,\"code\":\"RSH7ZFZ99FHA\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-08 12:00:42\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-07 12:00:42\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 12:00:42', '2026-01-28 12:11:44'),
(4, 'TOKEN_CREATED', 'teste@teste.com', 'Teste', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":1,\"full_name\":\"Teste\",\"email\":\"teste@teste.com\",\"document_number\":\"71044965053\",\"phone_number\":\"(11) 99999-9999\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2025-12-03 10:05:00\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2025-11-26 18:14:40\",\"updated_at\":\"2026-01-07 11:24:07\"},\"token\":{\"id\":24,\"portal_user_id\":1,\"code\":\"SXSJTG28D3V3\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-08 12:12:49\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-07 12:12:49\",\"user_name\":\"Teste\",\"user_email\":\"teste@teste.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 12:12:49', '2026-01-28 12:11:44'),
(5, 'TOKEN_CREATED', 'lais_rodrigues@oi.com.br', 'Laís Letícia Malu Rodrigues', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":2,\"full_name\":\"Laís Letícia Malu Rodrigues\",\"email\":\"lais_rodrigues@oi.com.br\",\"document_number\":\"08022819743\",\"phone_number\":\"(61) 98661-5844\",\"external_id\":\"\",\"notes\":\"sadbs\\\\bs\\\\b\\\\sdb\\\\sdb\\\\sbd\",\"status\":\"INVITED\",\"last_login_at\":\"2025-12-02 16:42:50\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2025-12-02 16:26:38\",\"updated_at\":\"2025-12-02 16:42:50\"},\"token\":{\"id\":25,\"portal_user_id\":2,\"code\":\"EKRFZZ9B4AW2\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-07 13:28:50\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-07 12:28:50\",\"user_name\":\"Laís Letícia Malu Rodrigues\",\"user_email\":\"lais_rodrigues@oi.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 12:28:50', '2026-01-28 12:11:44'),
(6, 'TOKEN_CREATED', 'teste@teste.com', 'Teste', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":1,\"full_name\":\"Teste\",\"email\":\"teste@teste.com\",\"document_number\":\"71044965053\",\"phone_number\":\"(11) 99999-9999\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2025-12-03 10:05:00\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2025-11-26 18:14:40\",\"updated_at\":\"2026-01-07 11:24:07\"},\"token\":{\"id\":26,\"portal_user_id\":1,\"code\":\"J37J9BGB2ZX4\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-08 12:38:47\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-07 12:38:47\",\"user_name\":\"Teste\",\"user_email\":\"teste@teste.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 12:38:47', '2026-01-28 12:11:44'),
(7, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":null},\"token\":{\"id\":27,\"portal_user_id\":3,\"code\":\"9GZRD7UTHX6T\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-08 14:46:06\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-07 14:46:06\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 14:46:06', '2026-01-28 12:11:45'),
(8, 'SUBMISSION_RECEIVED', 'admin@nimbusdocs.local', 'Administrador do Sistema', '[NimbusDocs] Nova submissão recebida', 'submission_received', '{\"submission\":{\"id\":5,\"portal_user_id\":3,\"reference_code\":\"SUB-20260107-K7X3N887\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Isis Tatiane Baptista\",\"company_cnpj\":\"52004807000368\",\"company_name\":\"MICROSOFT 272945 BRASIL LTDA\",\"main_activity\":\"Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet\",\"phone\":\"(11) 5504-2155\",\"website\":\"https:\\/\\/www.microsoft.com\\/pt-br\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000000.00\",\"is_us_person\":0,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Oliver Lorenzo da Rosa\",\"registrant_position\":\"Diretor\",\"registrant_rg\":\"26.069.550-6\",\"registrant_cpf\":\"95039350830\",\"status\":\"PENDING\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/143.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-07 14:54:09\",\"status_updated_at\":null,\"status_updated_by\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2026-01-07 14:46:25\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-07 14:46:25\"},\"admin\":{\"id\":5,\"name\":\"Administrador\",\"email\":\"admin@nimbusdocs.local\",\"full_name\":\"Administrador do Sistema\",\"azure_oid\":null,\"azure_tenant_id\":null,\"azure_upn\":null,\"password_hash\":\"$2y$10$NJLmdPmCRL0Yw2wnDeGWouiYCxIlXa.77nu\\/Id1vO4x2qbb9438AG\",\"two_factor_secret\":\"XIGHVC6NNL67PIFX\",\"two_factor_enabled\":0,\"two_factor_confirmed_at\":null,\"auth_mode\":\"LOCAL_ONLY\",\"role\":\"SUPER_ADMIN\",\"is_active\":1,\"status\":\"ACTIVE\",\"ms_object_id\":null,\"ms_tenant_id\":null,\"ms_upn\":null,\"last_login_at\":\"2026-01-07 11:55:02\",\"last_login_provider\":\"LOCAL\",\"created_at\":\"2026-01-05 15:53:04\",\"updated_at\":\"2026-01-07 11:59:51\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 14:54:09', '2026-01-28 12:11:45'),
(9, 'SUBMISSION_RECEIVED', 'anderson.cavalcante@bsicapital.com.br', 'Anderson Barbosa', '[NimbusDocs] Nova submissão recebida', 'submission_received', '{\"submission\":{\"id\":5,\"portal_user_id\":3,\"reference_code\":\"SUB-20260107-K7X3N887\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Isis Tatiane Baptista\",\"company_cnpj\":\"52004807000368\",\"company_name\":\"MICROSOFT 272945 BRASIL LTDA\",\"main_activity\":\"Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet\",\"phone\":\"(11) 5504-2155\",\"website\":\"https:\\/\\/www.microsoft.com\\/pt-br\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000000.00\",\"is_us_person\":0,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Oliver Lorenzo da Rosa\",\"registrant_position\":\"Diretor\",\"registrant_rg\":\"26.069.550-6\",\"registrant_cpf\":\"95039350830\",\"status\":\"PENDING\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/143.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-07 14:54:09\",\"status_updated_at\":null,\"status_updated_by\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2026-01-07 14:46:25\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-07 14:46:25\"},\"admin\":{\"id\":1,\"name\":\"Anderson Barbosa\",\"email\":\"anderson.cavalcante@bsicapital.com.br\",\"full_name\":\"Anderson Barbosa\",\"azure_oid\":null,\"azure_tenant_id\":null,\"azure_upn\":null,\"password_hash\":\"$2y$10$qb4OJVAHCI.Z09B0lTmf4ej98GD9jq5nOh2pdaYxCD.q.ZwAISZG2\",\"two_factor_secret\":null,\"two_factor_enabled\":0,\"two_factor_confirmed_at\":null,\"auth_mode\":\"LOCAL_ONLY\",\"role\":\"SUPER_ADMIN\",\"is_active\":1,\"status\":\"ACTIVE\",\"ms_object_id\":null,\"ms_tenant_id\":null,\"ms_upn\":null,\"last_login_at\":\"2026-01-07 14:45:48\",\"last_login_provider\":\"LOCAL\",\"created_at\":\"2025-11-12 11:03:45\",\"updated_at\":\"2026-01-07 14:45:48\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 14:54:09', '2026-01-28 12:11:45'),
(10, 'SUBMISSION_RECEIVED', 'admin@nimbusdocs.local', 'Administrador do Sistema', '[NimbusDocs] Nova submissão recebida', 'submission_received', '{\"submission\":{\"id\":6,\"portal_user_id\":3,\"reference_code\":\"SUB-20260107-2DBYB6TN\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Diego João Souza\",\"company_cnpj\":\"52004807000368\",\"company_name\":\"MICROSOFT 272945 BRASIL LTDA\",\"main_activity\":\"Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet\",\"phone\":\"(11) 5504-2155\",\"website\":\"https:\\/\\/www.microsoft.com\\/pt-br\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000.00\",\"is_us_person\":1,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Fernando Rodrigo Pietro Mendes\",\"registrant_position\":\"Gerente\",\"registrant_rg\":\"22.636.412-4\",\"registrant_cpf\":\"23100249887\",\"status\":\"PENDING\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/143.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-07 15:20:29\",\"status_updated_at\":null,\"status_updated_by\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2026-01-07 14:46:25\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-07 14:46:25\"},\"admin\":{\"id\":5,\"name\":\"Administrador\",\"email\":\"admin@nimbusdocs.local\",\"full_name\":\"Administrador do Sistema\",\"azure_oid\":null,\"azure_tenant_id\":null,\"azure_upn\":null,\"password_hash\":\"$2y$10$NJLmdPmCRL0Yw2wnDeGWouiYCxIlXa.77nu\\/Id1vO4x2qbb9438AG\",\"two_factor_secret\":\"XIGHVC6NNL67PIFX\",\"two_factor_enabled\":0,\"two_factor_confirmed_at\":null,\"auth_mode\":\"LOCAL_ONLY\",\"role\":\"SUPER_ADMIN\",\"is_active\":1,\"status\":\"ACTIVE\",\"ms_object_id\":null,\"ms_tenant_id\":null,\"ms_upn\":null,\"last_login_at\":\"2026-01-07 11:55:02\",\"last_login_provider\":\"LOCAL\",\"created_at\":\"2026-01-05 15:53:04\",\"updated_at\":\"2026-01-07 11:59:51\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 15:20:29', '2026-01-28 12:11:46'),
(11, 'SUBMISSION_RECEIVED', 'anderson.cavalcante@bsicapital.com.br', 'Anderson Barbosa', '[NimbusDocs] Nova submissão recebida', 'submission_received', '{\"submission\":{\"id\":6,\"portal_user_id\":3,\"reference_code\":\"SUB-20260107-2DBYB6TN\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Diego João Souza\",\"company_cnpj\":\"52004807000368\",\"company_name\":\"MICROSOFT 272945 BRASIL LTDA\",\"main_activity\":\"Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet\",\"phone\":\"(11) 5504-2155\",\"website\":\"https:\\/\\/www.microsoft.com\\/pt-br\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000.00\",\"is_us_person\":1,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Fernando Rodrigo Pietro Mendes\",\"registrant_position\":\"Gerente\",\"registrant_rg\":\"22.636.412-4\",\"registrant_cpf\":\"23100249887\",\"status\":\"PENDING\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/143.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-07 15:20:29\",\"status_updated_at\":null,\"status_updated_by\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2026-01-07 14:46:25\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-07 14:46:25\"},\"admin\":{\"id\":1,\"name\":\"Anderson Barbosa\",\"email\":\"anderson.cavalcante@bsicapital.com.br\",\"full_name\":\"Anderson Barbosa\",\"azure_oid\":null,\"azure_tenant_id\":null,\"azure_upn\":null,\"password_hash\":\"$2y$10$qb4OJVAHCI.Z09B0lTmf4ej98GD9jq5nOh2pdaYxCD.q.ZwAISZG2\",\"two_factor_secret\":null,\"two_factor_enabled\":0,\"two_factor_confirmed_at\":null,\"auth_mode\":\"LOCAL_ONLY\",\"role\":\"SUPER_ADMIN\",\"is_active\":1,\"status\":\"ACTIVE\",\"ms_object_id\":null,\"ms_tenant_id\":null,\"ms_upn\":null,\"last_login_at\":\"2026-01-07 14:45:48\",\"last_login_provider\":\"LOCAL\",\"created_at\":\"2025-11-12 11:03:45\",\"updated_at\":\"2026-01-07 14:45:48\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 15:20:29', '2026-01-28 12:11:46'),
(12, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2026-01-07 14:46:25\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-07 14:46:25\"},\"token\":{\"id\":28,\"portal_user_id\":3,\"code\":\"J7GBCYCKYTW2\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-14 15:27:00\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-07 15:27:00\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 15:27:00', '2026-01-28 12:11:46'),
(13, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2026-01-07 15:27:28\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-07 15:27:28\"},\"token\":{\"id\":29,\"portal_user_id\":3,\"code\":\"AXF85PHDV9WT\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-08 15:51:19\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-07 15:51:19\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-07 15:51:19', '2026-01-28 12:11:47'),
(14, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2026-01-07 15:51:39\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-07 15:51:39\"},\"token\":{\"id\":30,\"portal_user_id\":3,\"code\":\"FTUF9W3EW7G5\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-09 10:47:44\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-08 10:47:44\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-08 10:47:44', '2026-01-28 12:11:47'),
(15, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":\"2026-01-08 10:47:50\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-08 10:47:50\"},\"token\":{\"id\":31,\"portal_user_id\":3,\"code\":\"KM8TWT3WGZ2Y\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-09 11:38:33\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-08 11:38:33\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-08 11:38:33', '2026-01-28 12:11:47'),
(16, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-08 11:38:42\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-08 15:28:08\"},\"token\":{\"id\":32,\"portal_user_id\":3,\"code\":\"XK4H6HGBXWE9\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-13 16:10:19\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-12 16:10:19\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-12 16:10:19', '2026-01-28 12:11:48'),
(17, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-12 16:10:27\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-12 16:10:27\"},\"token\":{\"id\":33,\"portal_user_id\":3,\"code\":\"G3BW7JAU4ASJ\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-13 16:31:16\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-12 16:31:16\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-12 16:31:16', '2026-01-28 12:11:48'),
(18, 'SUBMISSION_STATUS_CHANGED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Atualização da sua submissão: Cadastro de Cliente (PENDING → COMPLETED)', 'submission_status_changed', '{\"submission\":{\"id\":6,\"portal_user_id\":3,\"reference_code\":\"SUB-20260107-2DBYB6TN\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Diego João Souza\",\"company_cnpj\":\"52004807000368\",\"company_name\":\"MICROSOFT 272945 BRASIL LTDA\",\"main_activity\":\"Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet\",\"phone\":\"(11) 5504-2155\",\"website\":\"https:\\/\\/www.microsoft.com\\/pt-br\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000.00\",\"is_us_person\":1,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Fernando Rodrigo Pietro Mendes\",\"registrant_position\":\"Gerente\",\"registrant_rg\":\"22.636.412-4\",\"registrant_cpf\":\"23100249887\",\"status\":\"COMPLETED\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/143.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-07 15:20:29\",\"status_updated_at\":\"2026-01-26 11:54:41\",\"status_updated_by\":1},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-12 16:46:54\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-12 17:00:21\"},\"oldStatus\":\"PENDING\",\"newStatus\":\"COMPLETED\"}', 'SENT', 0, 5, NULL, NULL, '2026-01-26 11:54:41', '2026-01-28 12:11:48'),
(19, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-12 16:46:54\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-12 17:00:21\"},\"token\":{\"id\":34,\"portal_user_id\":3,\"code\":\"DDSQU2ACT4ME\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-27 12:00:40\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-26 12:00:40\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-26 12:00:40', '2026-01-28 12:11:48'),
(20, 'SUBMISSION_RECEIVED', 'admin@nimbusdocs.local', 'Administrador do Sistema', '[NimbusDocs] Nova submissão recebida', 'submission_received', '{\"submission\":{\"id\":7,\"portal_user_id\":3,\"reference_code\":\"SUB-20260126-ZQYBMBSN\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Diego João Souza\",\"company_cnpj\":\"52004807000368\",\"company_name\":\"MICROSOFT 272945 BRASIL LTDA\",\"main_activity\":\"Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet\",\"phone\":\"(11) 5504-2155\",\"website\":\"\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000.00\",\"is_us_person\":0,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Isabel Isadora de Paula\",\"registrant_position\":\"Teste\",\"registrant_rg\":\"12.110.273-7\",\"registrant_cpf\":\"65195364583\",\"status\":\"PENDING\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/144.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-26 12:55:23\",\"status_updated_at\":null,\"status_updated_by\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-26 12:00:45\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-26 12:00:45\"},\"admin\":{\"id\":5,\"name\":\"Administrador\",\"email\":\"admin@nimbusdocs.local\",\"full_name\":\"Administrador do Sistema\",\"azure_oid\":null,\"azure_tenant_id\":null,\"azure_upn\":null,\"password_hash\":\"$2y$10$NJLmdPmCRL0Yw2wnDeGWouiYCxIlXa.77nu\\/Id1vO4x2qbb9438AG\",\"two_factor_secret\":\"XIGHVC6NNL67PIFX\",\"two_factor_enabled\":0,\"two_factor_confirmed_at\":null,\"auth_mode\":\"LOCAL_ONLY\",\"role\":\"SUPER_ADMIN\",\"is_active\":1,\"status\":\"ACTIVE\",\"ms_object_id\":null,\"ms_tenant_id\":null,\"ms_upn\":null,\"last_login_at\":\"2026-01-07 11:55:02\",\"last_login_provider\":\"LOCAL\",\"created_at\":\"2026-01-05 15:53:04\",\"updated_at\":\"2026-01-07 11:59:51\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-26 12:55:23', '2026-01-28 12:11:49'),
(21, 'SUBMISSION_RECEIVED', 'anderson.cavalcante@bsicapital.com.br', 'Anderson Barbosa', '[NimbusDocs] Nova submissão recebida', 'submission_received', '{\"submission\":{\"id\":7,\"portal_user_id\":3,\"reference_code\":\"SUB-20260126-ZQYBMBSN\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Diego João Souza\",\"company_cnpj\":\"52004807000368\",\"company_name\":\"MICROSOFT 272945 BRASIL LTDA\",\"main_activity\":\"Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet\",\"phone\":\"(11) 5504-2155\",\"website\":\"\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000.00\",\"is_us_person\":0,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Isabel Isadora de Paula\",\"registrant_position\":\"Teste\",\"registrant_rg\":\"12.110.273-7\",\"registrant_cpf\":\"65195364583\",\"status\":\"PENDING\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/144.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-26 12:55:23\",\"status_updated_at\":null,\"status_updated_by\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-26 12:00:45\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-26 12:00:45\"},\"admin\":{\"id\":1,\"name\":\"Anderson Barbosa\",\"email\":\"anderson.cavalcante@bsicapital.com.br\",\"full_name\":\"Anderson Barbosa\",\"azure_oid\":null,\"azure_tenant_id\":null,\"azure_upn\":null,\"password_hash\":\"$2y$10$qb4OJVAHCI.Z09B0lTmf4ej98GD9jq5nOh2pdaYxCD.q.ZwAISZG2\",\"two_factor_secret\":\"VQ5LSKXI52Q4GNT5\",\"two_factor_enabled\":0,\"two_factor_confirmed_at\":null,\"auth_mode\":\"LOCAL_ONLY\",\"role\":\"SUPER_ADMIN\",\"is_active\":1,\"status\":\"ACTIVE\",\"ms_object_id\":null,\"ms_tenant_id\":null,\"ms_upn\":null,\"last_login_at\":\"2026-01-26 11:08:46\",\"last_login_provider\":\"LOCAL\",\"created_at\":\"2025-11-12 11:03:45\",\"updated_at\":\"2026-01-26 11:08:46\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-26 12:55:23', '2026-01-28 12:14:50'),
(22, 'SUBMISSION_RECEIVED', 'admin@nimbusdocs.local', 'Administrador do Sistema', '[NimbusDocs] Nova submissão recebida', 'submission_received', '{\"submission\":{\"id\":8,\"portal_user_id\":3,\"reference_code\":\"SUB-20260126-DTFBGNU9\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Alice Sophie Heloisa Duarte\",\"company_cnpj\":\"04712500000107\",\"company_name\":\"MICROSOFT DO BRASIL IMPORTACAO E COMERCIO DE SOFTWARE E VIDEO GAMES LTDA\",\"main_activity\":\"Desenvolvimento e licenciamento de programas de computador não-customizáveis\",\"phone\":\"(11) 5504-2155\",\"website\":\"https:\\/\\/www.microsoft.com\\/pt-br\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000.00\",\"is_us_person\":0,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Kaique Gustavo Eduardo Gomes\",\"registrant_position\":\"Teste\",\"registrant_rg\":\"35.107.366-8\",\"registrant_cpf\":\"65837859861\",\"status\":\"PENDING\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/144.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-26 13:05:30\",\"status_updated_at\":null,\"status_updated_by\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-26 12:00:45\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-26 12:00:45\"},\"admin\":{\"id\":5,\"name\":\"Administrador\",\"email\":\"admin@nimbusdocs.local\",\"full_name\":\"Administrador do Sistema\",\"azure_oid\":null,\"azure_tenant_id\":null,\"azure_upn\":null,\"password_hash\":\"$2y$10$NJLmdPmCRL0Yw2wnDeGWouiYCxIlXa.77nu\\/Id1vO4x2qbb9438AG\",\"two_factor_secret\":\"XIGHVC6NNL67PIFX\",\"two_factor_enabled\":0,\"two_factor_confirmed_at\":null,\"auth_mode\":\"LOCAL_ONLY\",\"role\":\"SUPER_ADMIN\",\"is_active\":1,\"status\":\"ACTIVE\",\"ms_object_id\":null,\"ms_tenant_id\":null,\"ms_upn\":null,\"last_login_at\":\"2026-01-07 11:55:02\",\"last_login_provider\":\"LOCAL\",\"created_at\":\"2026-01-05 15:53:04\",\"updated_at\":\"2026-01-07 11:59:51\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-26 13:05:30', '2026-01-28 12:14:51'),
(23, 'SUBMISSION_RECEIVED', 'anderson.cavalcante@bsicapital.com.br', 'Anderson Barbosa', '[NimbusDocs] Nova submissão recebida', 'submission_received', '{\"submission\":{\"id\":8,\"portal_user_id\":3,\"reference_code\":\"SUB-20260126-DTFBGNU9\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Alice Sophie Heloisa Duarte\",\"company_cnpj\":\"04712500000107\",\"company_name\":\"MICROSOFT DO BRASIL IMPORTACAO E COMERCIO DE SOFTWARE E VIDEO GAMES LTDA\",\"main_activity\":\"Desenvolvimento e licenciamento de programas de computador não-customizáveis\",\"phone\":\"(11) 5504-2155\",\"website\":\"https:\\/\\/www.microsoft.com\\/pt-br\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000.00\",\"is_us_person\":0,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Kaique Gustavo Eduardo Gomes\",\"registrant_position\":\"Teste\",\"registrant_rg\":\"35.107.366-8\",\"registrant_cpf\":\"65837859861\",\"status\":\"PENDING\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/144.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-26 13:05:30\",\"status_updated_at\":null,\"status_updated_by\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-26 12:00:45\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-26 12:00:45\"},\"admin\":{\"id\":1,\"name\":\"Anderson Barbosa\",\"email\":\"anderson.cavalcante@bsicapital.com.br\",\"full_name\":\"Anderson Barbosa\",\"azure_oid\":null,\"azure_tenant_id\":null,\"azure_upn\":null,\"password_hash\":\"$2y$10$qb4OJVAHCI.Z09B0lTmf4ej98GD9jq5nOh2pdaYxCD.q.ZwAISZG2\",\"two_factor_secret\":\"VQ5LSKXI52Q4GNT5\",\"two_factor_enabled\":0,\"two_factor_confirmed_at\":null,\"auth_mode\":\"LOCAL_ONLY\",\"role\":\"SUPER_ADMIN\",\"is_active\":1,\"status\":\"ACTIVE\",\"ms_object_id\":null,\"ms_tenant_id\":null,\"ms_upn\":null,\"last_login_at\":\"2026-01-26 11:08:46\",\"last_login_provider\":\"LOCAL\",\"created_at\":\"2025-11-12 11:03:45\",\"updated_at\":\"2026-01-26 11:08:46\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-26 13:05:30', '2026-01-28 12:14:51'),
(24, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-26 12:00:45\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-26 12:00:45\"},\"token\":{\"id\":35,\"portal_user_id\":3,\"code\":\"KSSF8QW5EN3Q\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-28 10:19:25\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-27 10:19:25\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-27 10:19:25', '2026-01-28 12:14:51'),
(25, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-27 10:19:28\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-27 10:19:28\"},\"token\":{\"id\":36,\"portal_user_id\":3,\"code\":\"DYA76V87GWHP\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-28 12:06:05\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-27 12:06:05\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-27 12:06:05', '2026-01-28 12:14:52'),
(26, 'USER_PRECREATED', 'luizdanilopereira@velc.com.br', 'Luiz Danilo João Pereira', '[NimbusDocs] Acesso ao portal NimbusDocs', 'user_precreated', '{\"user\":{\"id\":4,\"full_name\":\"Luiz Danilo João Pereira\",\"email\":\"luizdanilopereira@velc.com.br\",\"document_number\":\"31757154515\",\"phone_number\":\"(66) 98782-2754\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-28 11:00:38\",\"updated_at\":null},\"token\":null}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 11:00:38', '2026-01-28 12:14:52'),
(27, 'TOKEN_CREATED', 'luizdanilopereira@velc.com.br', 'Luiz Danilo João Pereira', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":4,\"full_name\":\"Luiz Danilo João Pereira\",\"email\":\"luizdanilopereira@velc.com.br\",\"document_number\":\"31757154515\",\"phone_number\":\"(66) 98782-2754\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-28 11:00:38\",\"updated_at\":null},\"token\":{\"id\":37,\"portal_user_id\":4,\"code\":\"V49H4RPBS456\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-29 11:01:15\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-28 11:01:15\",\"user_name\":\"Luiz Danilo João Pereira\",\"user_email\":\"luizdanilopereira@velc.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 11:01:15', '2026-01-28 12:14:52'),
(28, 'NEW_GENERAL_DOCUMENT', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', 'Novo documento disponível: Teste II', 'new_general_document', '{\"doc\":{\"id\":5,\"category_id\":4,\"title\":\"Teste II\",\"description\":\"kndfkbvndsfbnvdkfjbvsd\",\"file_path\":\"C:\\\\xampp\\\\htdocs\\\\NimbusDocs\\\\src\\\\Presentation\\\\Controller\\\\Admin\\/..\\/..\\/..\\/..\\/storage\\/general_documents\\/\\\\d6a51ec7cb722ef81edf00548c532337.pdf\",\"file_mime\":\"application\\/pdf\",\"file_size\":931763,\"file_original_name\":\"Complete_com_o_Docusign_AGT_CRA_REDENTOR_Con_removed.pdf\",\"is_active\":1,\"published_at\":\"2026-01-28 11:38:45\",\"created_by_admin\":1,\"created_at\":\"2026-01-28 11:38:45\",\"category_name\":\"Teste\"},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 11:38:45', '2026-01-28 12:14:52'),
(29, 'NEW_GENERAL_DOCUMENT', 'lais_rodrigues@oi.com.br', 'Laís Letícia Malu Rodrigues', 'Novo documento disponível: Teste II', 'new_general_document', '{\"doc\":{\"id\":5,\"category_id\":4,\"title\":\"Teste II\",\"description\":\"kndfkbvndsfbnvdkfjbvsd\",\"file_path\":\"C:\\\\xampp\\\\htdocs\\\\NimbusDocs\\\\src\\\\Presentation\\\\Controller\\\\Admin\\/..\\/..\\/..\\/..\\/storage\\/general_documents\\/\\\\d6a51ec7cb722ef81edf00548c532337.pdf\",\"file_mime\":\"application\\/pdf\",\"file_size\":931763,\"file_original_name\":\"Complete_com_o_Docusign_AGT_CRA_REDENTOR_Con_removed.pdf\",\"is_active\":1,\"published_at\":\"2026-01-28 11:38:45\",\"created_by_admin\":1,\"created_at\":\"2026-01-28 11:38:45\",\"category_name\":\"Teste\"},\"user\":{\"id\":2,\"full_name\":\"Laís Letícia Malu Rodrigues\",\"email\":\"lais_rodrigues@oi.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 11:38:45', '2026-01-28 12:14:53'),
(30, 'NEW_GENERAL_DOCUMENT', 'luizdanilopereira@velc.com.br', 'Luiz Danilo João Pereira', 'Novo documento disponível: Teste II', 'new_general_document', '{\"doc\":{\"id\":5,\"category_id\":4,\"title\":\"Teste II\",\"description\":\"kndfkbvndsfbnvdkfjbvsd\",\"file_path\":\"C:\\\\xampp\\\\htdocs\\\\NimbusDocs\\\\src\\\\Presentation\\\\Controller\\\\Admin\\/..\\/..\\/..\\/..\\/storage\\/general_documents\\/\\\\d6a51ec7cb722ef81edf00548c532337.pdf\",\"file_mime\":\"application\\/pdf\",\"file_size\":931763,\"file_original_name\":\"Complete_com_o_Docusign_AGT_CRA_REDENTOR_Con_removed.pdf\",\"is_active\":1,\"published_at\":\"2026-01-28 11:38:45\",\"created_by_admin\":1,\"created_at\":\"2026-01-28 11:38:45\",\"category_name\":\"Teste\"},\"user\":{\"id\":4,\"full_name\":\"Luiz Danilo João Pereira\",\"email\":\"luizdanilopereira@velc.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 11:38:45', '2026-01-28 12:14:53'),
(31, 'NEW_GENERAL_DOCUMENT', 'teste@teste.com', 'Teste', 'Novo documento disponível: Teste II', 'new_general_document', '{\"doc\":{\"id\":5,\"category_id\":4,\"title\":\"Teste II\",\"description\":\"kndfkbvndsfbnvdkfjbvsd\",\"file_path\":\"C:\\\\xampp\\\\htdocs\\\\NimbusDocs\\\\src\\\\Presentation\\\\Controller\\\\Admin\\/..\\/..\\/..\\/..\\/storage\\/general_documents\\/\\\\d6a51ec7cb722ef81edf00548c532337.pdf\",\"file_mime\":\"application\\/pdf\",\"file_size\":931763,\"file_original_name\":\"Complete_com_o_Docusign_AGT_CRA_REDENTOR_Con_removed.pdf\",\"is_active\":1,\"published_at\":\"2026-01-28 11:38:45\",\"created_by_admin\":1,\"created_at\":\"2026-01-28 11:38:45\",\"category_name\":\"Teste\"},\"user\":{\"id\":1,\"full_name\":\"Teste\",\"email\":\"teste@teste.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 11:38:45', '2026-01-28 12:14:53'),
(32, 'NEW_ANNOUNCEMENT', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Novo comunicado: Teste', 'new_announcement', '{\"announcement\":{\"id\":4,\"title\":\"Teste\",\"body\":\"Sed consectetur ultrices tortor sed faucibus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse ac nulla massa. Pellentesque quis odio tincidunt, vulputate nulla nec, maximus velit. Nunc condimentum mollis odio, in dapibus eros pretium sit amet. Duis vel risus sed libero tincidunt placerat in quis mauris. Maecenas sed consequat nunc. Donec in purus a lacus malesuada varius ac sit amet purus. Nulla facilisi. Aliquam posuere lacus a lacus molestie, in consectetur augue euismod. Pellentesque volutpat sodales nisi. In ac elementum mi.\",\"level\":\"info\",\"starts_at\":null,\"ends_at\":null,\"is_active\":1,\"created_by_admin\":1,\"created_at\":\"2026-01-28 11:50:48\",\"updated_at\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 11:50:48', '2026-01-28 12:14:54'),
(33, 'NEW_ANNOUNCEMENT', 'lais_rodrigues@oi.com.br', 'Laís Letícia Malu Rodrigues', '[NimbusDocs] Novo comunicado: Teste', 'new_announcement', '{\"announcement\":{\"id\":4,\"title\":\"Teste\",\"body\":\"Sed consectetur ultrices tortor sed faucibus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse ac nulla massa. Pellentesque quis odio tincidunt, vulputate nulla nec, maximus velit. Nunc condimentum mollis odio, in dapibus eros pretium sit amet. Duis vel risus sed libero tincidunt placerat in quis mauris. Maecenas sed consequat nunc. Donec in purus a lacus malesuada varius ac sit amet purus. Nulla facilisi. Aliquam posuere lacus a lacus molestie, in consectetur augue euismod. Pellentesque volutpat sodales nisi. In ac elementum mi.\",\"level\":\"info\",\"starts_at\":null,\"ends_at\":null,\"is_active\":1,\"created_by_admin\":1,\"created_at\":\"2026-01-28 11:50:48\",\"updated_at\":null},\"user\":{\"id\":2,\"full_name\":\"Laís Letícia Malu Rodrigues\",\"email\":\"lais_rodrigues@oi.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 11:50:48', '2026-01-28 12:14:54'),
(34, 'NEW_ANNOUNCEMENT', 'luizdanilopereira@velc.com.br', 'Luiz Danilo João Pereira', '[NimbusDocs] Novo comunicado: Teste', 'new_announcement', '{\"announcement\":{\"id\":4,\"title\":\"Teste\",\"body\":\"Sed consectetur ultrices tortor sed faucibus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse ac nulla massa. Pellentesque quis odio tincidunt, vulputate nulla nec, maximus velit. Nunc condimentum mollis odio, in dapibus eros pretium sit amet. Duis vel risus sed libero tincidunt placerat in quis mauris. Maecenas sed consequat nunc. Donec in purus a lacus malesuada varius ac sit amet purus. Nulla facilisi. Aliquam posuere lacus a lacus molestie, in consectetur augue euismod. Pellentesque volutpat sodales nisi. In ac elementum mi.\",\"level\":\"info\",\"starts_at\":null,\"ends_at\":null,\"is_active\":1,\"created_by_admin\":1,\"created_at\":\"2026-01-28 11:50:48\",\"updated_at\":null},\"user\":{\"id\":4,\"full_name\":\"Luiz Danilo João Pereira\",\"email\":\"luizdanilopereira@velc.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 11:50:48', '2026-01-28 12:14:54'),
(35, 'NEW_ANNOUNCEMENT', 'teste@teste.com', 'Teste', '[NimbusDocs] Novo comunicado: Teste', 'new_announcement', '{\"announcement\":{\"id\":4,\"title\":\"Teste\",\"body\":\"Sed consectetur ultrices tortor sed faucibus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse ac nulla massa. Pellentesque quis odio tincidunt, vulputate nulla nec, maximus velit. Nunc condimentum mollis odio, in dapibus eros pretium sit amet. Duis vel risus sed libero tincidunt placerat in quis mauris. Maecenas sed consequat nunc. Donec in purus a lacus malesuada varius ac sit amet purus. Nulla facilisi. Aliquam posuere lacus a lacus molestie, in consectetur augue euismod. Pellentesque volutpat sodales nisi. In ac elementum mi.\",\"level\":\"info\",\"starts_at\":null,\"ends_at\":null,\"is_active\":1,\"created_by_admin\":1,\"created_at\":\"2026-01-28 11:50:48\",\"updated_at\":null},\"user\":{\"id\":1,\"full_name\":\"Teste\",\"email\":\"teste@teste.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 11:50:48', '2026-01-28 12:14:54'),
(36, 'USER_PRECREATED', 'andersoncavalcantr96@hotmail.com', 'Maria Eliane Nicole Teixeira', '[NimbusDocs] Acesso ao portal NimbusDocs', 'user_precreated', '{\"user\":{\"id\":5,\"full_name\":\"Maria Eliane Nicole Teixeira\",\"email\":\"andersoncavalcantr96@hotmail.com\",\"document_number\":\"23046005148\",\"phone_number\":\"(47) 99334-3927\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"INVITED\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-28 14:48:39\",\"updated_at\":null},\"token\":null}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 14:48:39', '2026-01-28 14:48:44'),
(37, 'TOKEN_CREATED', 'andersoncavalcantr96@hotmail.com', 'Maria Eliane Nicole Teixeira', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":5,\"full_name\":\"Maria Eliane Nicole Teixeira\",\"email\":\"andersoncavalcantr96@hotmail.com\",\"document_number\":\"23046005148\",\"phone_number\":\"(47) 99334-3927\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-28 14:48:39\",\"updated_at\":\"2026-01-28 14:48:47\"},\"token\":{\"id\":38,\"portal_user_id\":5,\"code\":\"5ZT2U43WQTNR\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-29 14:56:01\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-28 14:56:01\",\"user_name\":\"Maria Eliane Nicole Teixeira\",\"user_email\":\"andersoncavalcantr96@hotmail.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 14:56:01', '2026-01-28 14:56:05'),
(38, 'TOKEN_CREATED', 'benjamin-carvalho85@centerdiesel.com.br', 'Benjamin Paulo Osvaldo Carvalho', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-27 12:06:16\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-27 12:06:16\"},\"token\":{\"id\":39,\"portal_user_id\":3,\"code\":\"X2JW62KF7MNA\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-29 16:16:40\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-28 16:16:40\",\"user_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"user_email\":\"benjamin-carvalho85@centerdiesel.com.br\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 16:16:40', '2026-01-28 16:16:43');
INSERT INTO `notification_outbox` (`id`, `type`, `recipient_email`, `recipient_name`, `subject`, `template`, `payload_json`, `status`, `attempts`, `max_attempts`, `next_attempt_at`, `last_error`, `created_at`, `sent_at`) VALUES
(39, 'SUBMISSION_RECEIVED', 'admin@nimbusdocs.local', 'Administrador do Sistema', '[NimbusDocs] Nova submissão recebida', 'submission_received', '{\"submission\":{\"id\":9,\"portal_user_id\":3,\"reference_code\":\"SUB-20260128-UGQWB2VD\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Diego Colicchio\",\"company_cnpj\":\"02946761000166\",\"company_name\":\"RED BULL DO BRASIL LTDA.\",\"main_activity\":\"Comércio atacadista de cerveja, chope e refrigerante\",\"phone\":\"(11) 3016-2855\",\"website\":\"https:\\/\\/www.redbull.com\\/\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000.00\",\"is_us_person\":0,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Benedita Rafaela Nunes\",\"registrant_position\":\"Administrador\",\"registrant_rg\":\"27.105.568-6\",\"registrant_cpf\":\"27203665283\",\"status\":\"PENDING\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/144.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-28 16:44:16\",\"status_updated_at\":null,\"status_updated_by\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-28 16:34:32\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-28 16:34:32\"},\"admin\":{\"id\":5,\"name\":\"Administrador\",\"email\":\"admin@nimbusdocs.local\",\"full_name\":\"Administrador do Sistema\",\"azure_oid\":null,\"azure_tenant_id\":null,\"azure_upn\":null,\"password_hash\":\"$2y$10$NJLmdPmCRL0Yw2wnDeGWouiYCxIlXa.77nu\\/Id1vO4x2qbb9438AG\",\"two_factor_secret\":\"XIGHVC6NNL67PIFX\",\"two_factor_enabled\":0,\"two_factor_confirmed_at\":null,\"auth_mode\":\"LOCAL_ONLY\",\"role\":\"SUPER_ADMIN\",\"is_active\":1,\"status\":\"ACTIVE\",\"ms_object_id\":null,\"ms_tenant_id\":null,\"ms_upn\":null,\"last_login_at\":\"2026-01-07 11:55:02\",\"last_login_provider\":\"LOCAL\",\"created_at\":\"2026-01-05 15:53:04\",\"updated_at\":\"2026-01-07 11:59:51\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 16:44:16', '2026-01-28 16:44:19'),
(40, 'SUBMISSION_RECEIVED', 'anderson.cavalcante@bsicapital.com.br', 'Anderson Barbosa', '[NimbusDocs] Nova submissão recebida', 'submission_received', '{\"submission\":{\"id\":9,\"portal_user_id\":3,\"reference_code\":\"SUB-20260128-UGQWB2VD\",\"title\":\"Cadastro de Cliente\",\"message\":\"\",\"responsible_name\":\"Diego Colicchio\",\"company_cnpj\":\"02946761000166\",\"company_name\":\"RED BULL DO BRASIL LTDA.\",\"main_activity\":\"Comércio atacadista de cerveja, chope e refrigerante\",\"phone\":\"(11) 3016-2855\",\"website\":\"https:\\/\\/www.redbull.com\\/\",\"net_worth\":\"1000000000.00\",\"annual_revenue\":\"1000000000.00\",\"is_us_person\":0,\"is_pep\":0,\"shareholder_data\":null,\"registrant_name\":\"Benedita Rafaela Nunes\",\"registrant_position\":\"Administrador\",\"registrant_rg\":\"27.105.568-6\",\"registrant_cpf\":\"27203665283\",\"status\":\"PENDING\",\"created_ip\":\"127.0.0.1\",\"created_user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/144.0.0.0 Safari\\/537.36\",\"submitted_at\":\"2026-01-28 16:44:16\",\"status_updated_at\":null,\"status_updated_by\":null},\"user\":{\"id\":3,\"full_name\":\"Benjamin Paulo Osvaldo Carvalho\",\"email\":\"benjamin-carvalho85@centerdiesel.com.br\",\"document_number\":\"52059244544\",\"phone_number\":\"(96) 99362-4331\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-28 16:34:32\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-07 11:24:52\",\"updated_at\":\"2026-01-28 16:34:32\"},\"admin\":{\"id\":1,\"name\":\"Anderson Barbosa\",\"email\":\"anderson.cavalcante@bsicapital.com.br\",\"full_name\":\"Anderson Barbosa\",\"azure_oid\":null,\"azure_tenant_id\":null,\"azure_upn\":null,\"password_hash\":\"$2y$10$qb4OJVAHCI.Z09B0lTmf4ej98GD9jq5nOh2pdaYxCD.q.ZwAISZG2\",\"two_factor_secret\":\"UKT37CYBGSW4YP47\",\"two_factor_enabled\":0,\"two_factor_confirmed_at\":null,\"auth_mode\":\"LOCAL_ONLY\",\"role\":\"SUPER_ADMIN\",\"is_active\":1,\"status\":\"ACTIVE\",\"ms_object_id\":null,\"ms_tenant_id\":null,\"ms_upn\":null,\"last_login_at\":\"2026-01-28 14:47:43\",\"last_login_provider\":\"LOCAL\",\"created_at\":\"2025-11-12 11:03:45\",\"updated_at\":\"2026-01-28 16:33:56\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 16:44:16', '2026-01-28 16:44:19'),
(41, 'TOKEN_CREATED', 'andersoncavalcantr96@hotmail.com', 'Maria Eliane Nicole Teixeira', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":5,\"full_name\":\"Maria Eliane Nicole Teixeira\",\"email\":\"andersoncavalcantr96@hotmail.com\",\"document_number\":\"23046005148\",\"phone_number\":\"(47) 99334-3927\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-28 14:48:39\",\"updated_at\":\"2026-01-28 14:48:47\"},\"token\":{\"id\":40,\"portal_user_id\":5,\"code\":\"YWFTG4BX2FRZ\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-29 17:18:49\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-28 17:18:49\",\"user_name\":\"Maria Eliane Nicole Teixeira\",\"user_email\":\"andersoncavalcantr96@hotmail.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 17:18:49', '2026-01-28 17:18:55'),
(42, 'TOKEN_CREATED', 'andersoncavalcantr96@hotmail.com', 'Maria Eliane Nicole Teixeira', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":5,\"full_name\":\"Maria Eliane Nicole Teixeira\",\"email\":\"andersoncavalcantr96@hotmail.com\",\"document_number\":\"23046005148\",\"phone_number\":\"(47) 99334-3927\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-28 14:48:39\",\"updated_at\":\"2026-01-28 14:48:47\"},\"token\":{\"id\":41,\"portal_user_id\":5,\"code\":\"KYJWTBA6BDBV\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-29 17:19:29\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-28 17:19:29\",\"user_name\":\"Maria Eliane Nicole Teixeira\",\"user_email\":\"andersoncavalcantr96@hotmail.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 17:19:29', '2026-01-28 17:19:30'),
(43, 'TOKEN_CREATED', 'andersoncavalcantr96@hotmail.com', 'Maria Eliane Nicole Teixeira', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":5,\"full_name\":\"Maria Eliane Nicole Teixeira\",\"email\":\"andersoncavalcantr96@hotmail.com\",\"document_number\":\"23046005148\",\"phone_number\":\"(47) 99334-3927\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-28 14:48:39\",\"updated_at\":\"2026-01-28 14:48:47\"},\"token\":{\"id\":42,\"portal_user_id\":5,\"code\":\"DA7AJP8EMKMK\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-29 17:19:38\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-28 17:19:38\",\"user_name\":\"Maria Eliane Nicole Teixeira\",\"user_email\":\"andersoncavalcantr96@hotmail.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 17:19:38', '2026-01-28 17:19:40'),
(44, 'TOKEN_CREATED', 'andersoncavalcantr96@hotmail.com', 'Maria Eliane Nicole Teixeira', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":5,\"full_name\":\"Maria Eliane Nicole Teixeira\",\"email\":\"andersoncavalcantr96@hotmail.com\",\"document_number\":\"23046005148\",\"phone_number\":\"(47) 99334-3927\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-28 14:48:39\",\"updated_at\":\"2026-01-28 14:48:47\"},\"token\":{\"id\":43,\"portal_user_id\":5,\"code\":\"JMC22FC68EZJ\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-29 17:20:52\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-28 17:20:52\",\"user_name\":\"Maria Eliane Nicole Teixeira\",\"user_email\":\"andersoncavalcantr96@hotmail.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 17:20:52', '2026-01-28 17:20:56'),
(45, 'TOKEN_CREATED', 'andersoncavalcantr96@hotmail.com', 'Maria Eliane Nicole Teixeira', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":5,\"full_name\":\"Maria Eliane Nicole Teixeira\",\"email\":\"andersoncavalcantr96@hotmail.com\",\"document_number\":\"23046005148\",\"phone_number\":\"(47) 99334-3927\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":null,\"last_login_method\":null,\"created_at\":\"2026-01-28 14:48:39\",\"updated_at\":\"2026-01-28 14:48:47\"},\"token\":{\"id\":44,\"portal_user_id\":5,\"code\":\"AU5A7XDYQG48\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-29 17:21:44\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-28 17:21:44\",\"user_name\":\"Maria Eliane Nicole Teixeira\",\"user_email\":\"andersoncavalcantr96@hotmail.com\"}}', 'SENT', 0, 5, NULL, NULL, '2026-01-28 17:21:44', '2026-01-28 17:21:47'),
(46, 'TOKEN_CREATED', 'andersoncavalcantr96@hotmail.com', 'Maria Eliane Nicole Teixeira', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":5,\"full_name\":\"Maria Eliane Nicole Teixeira\",\"email\":\"andersoncavalcantr96@hotmail.com\",\"document_number\":\"23046005148\",\"phone_number\":\"(47) 99334-3927\",\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-28 17:21:50\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2026-01-28 14:48:39\",\"updated_at\":\"2026-01-28 17:21:50\"},\"token\":{\"id\":45,\"portal_user_id\":5,\"code\":\"TX9JXPVMWEZB\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-30 09:32:52\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-29 09:32:52\",\"user_name\":\"Maria Eliane Nicole Teixeira\",\"user_email\":\"andersoncavalcantr96@hotmail.com\"}}', 'PENDING', 0, 5, NULL, NULL, '2026-01-29 09:32:52', NULL),
(47, 'TOKEN_CREATED', 'teste@teste.com', 'Teste', '[NimbusDocs] Seu link de acesso ao portal', 'token_created', '{\"user\":{\"id\":1,\"full_name\":\"Teste\",\"email\":\"teste@teste.com\",\"document_number\":\"71044965053\",\"phone_number\":null,\"external_id\":\"\",\"notes\":\"\",\"status\":\"ACTIVE\",\"last_login_at\":\"2026-01-07 12:43:27\",\"last_login_method\":\"ACCESS_CODE\",\"created_at\":\"2025-11-26 18:14:40\",\"updated_at\":\"2026-01-08 15:27:59\"},\"token\":{\"id\":46,\"portal_user_id\":1,\"code\":\"UBRU2BT42UC8\",\"status\":\"PENDING\",\"expires_at\":\"2026-01-30 12:26:23\",\"used_at\":null,\"used_ip\":null,\"used_user_agent\":null,\"created_at\":\"2026-01-29 12:26:23\",\"user_name\":\"Teste\",\"user_email\":\"teste@teste.com\"}}', 'PENDING', 0, 5, NULL, NULL, '2026-01-29 12:26:23', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `admin_user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `admin_user_id`, `token`, `expires_at`, `used_at`, `created_at`) VALUES
(2, 1, '53504430aa830ead4697877feed9d5ce31ec1bceb577232149e10b033150bde9', '2025-12-19 10:58:55', NULL, '2025-12-19 12:58:55'),
(5, 5, '0903384b70cd8a55357991aa249e9521ca31b527a86f06886f240fd9c1b869d1', '2026-01-29 15:54:27', NULL, '2026-01-29 17:54:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `portal_access_log`
--

CREATE TABLE `portal_access_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `portal_user_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(50) NOT NULL,
  `resource_type` varchar(50) DEFAULT NULL,
  `resource_id` int(10) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `portal_access_log`
--

INSERT INTO `portal_access_log` (`id`, `portal_user_id`, `action`, `resource_type`, `resource_id`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-07 12:29:26'),
(2, 2, 'VIEW_SUBMISSION', 'submission', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-07 12:29:44'),
(3, 1, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 12:43:27'),
(4, 1, 'VIEW_SUBMISSION', 'submission', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 14:18:01'),
(5, 1, 'VIEW_SUBMISSION', 'submission', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 14:18:07'),
(6, 1, 'VIEW_SUBMISSION', 'submission', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 14:18:12'),
(7, 1, 'VIEW_SUBMISSION', 'submission', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 14:25:35'),
(8, 1, 'VIEW_SUBMISSION', 'submission', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 14:28:02'),
(9, 1, 'VIEW_SUBMISSION', 'submission', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 14:30:02'),
(10, 1, 'VIEW_SUBMISSION', 'submission', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 14:30:29'),
(11, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 14:46:25'),
(12, 3, 'VIEW_SUBMISSION', 'submission', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 14:54:09'),
(13, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:20:29'),
(14, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:25:39'),
(15, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:25:39'),
(16, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:25:43'),
(17, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:25:44'),
(18, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:25:51'),
(19, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:26:15'),
(20, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:26:16'),
(21, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:27:28'),
(22, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:27:35'),
(23, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:30:56'),
(24, 3, 'VIEW_SUBMISSION', 'submission', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:33:24'),
(25, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:33:35'),
(26, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:39:19'),
(27, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:39:20'),
(28, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:39:21'),
(29, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:39:21'),
(30, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:39:21'),
(31, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:40:00'),
(32, 3, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:50:10'),
(33, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:51:39'),
(34, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:51:41'),
(35, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-08 10:47:50'),
(36, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-08 11:38:42'),
(37, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 16:10:27'),
(38, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 16:46:54'),
(39, 3, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 17:17:32'),
(40, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 17:24:49'),
(41, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 12:00:45'),
(42, 3, 'VIEW_SUBMISSION', 'submission', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 12:00:50'),
(43, 3, 'VIEW_SUBMISSION', 'submission', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 12:10:54'),
(44, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 12:11:39'),
(45, 3, 'VIEW_SUBMISSION', 'submission', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 12:52:04'),
(46, 3, 'VIEW_SUBMISSION', 'submission', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 12:55:23'),
(47, 3, 'VIEW_SUBMISSION', 'submission', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 13:00:13'),
(48, 3, 'VIEW_SUBMISSION', 'submission', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 13:05:30'),
(49, 3, 'VIEW_SUBMISSION', 'submission', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 17:50:06'),
(50, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 10:19:28'),
(51, 3, 'VIEW_SUBMISSION', 'submission', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 11:08:37'),
(52, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 12:06:16'),
(53, 3, 'VIEW_SUBMISSION', 'submission', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 12:16:14'),
(54, 3, 'VIEW_SUBMISSION', 'submission', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 14:09:04'),
(55, 3, 'VIEW_SUBMISSION', 'submission', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 14:12:09'),
(56, 3, 'VIEW_SUBMISSION', 'submission', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 17:34:40'),
(57, 4, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 11:01:25'),
(58, 3, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 16:34:32'),
(59, 3, 'VIEW_SUBMISSION', 'submission', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 16:44:17'),
(60, 3, 'VIEW_SUBMISSION', 'submission', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 16:45:39'),
(61, 3, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 16:53:39'),
(62, 3, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 16:57:53'),
(63, 3, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 17:00:14'),
(64, 3, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 17:00:17'),
(65, 3, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 17:00:19'),
(66, 3, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 17:00:24'),
(67, 3, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 17:01:45'),
(68, 3, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 17:01:47'),
(69, 3, 'VIEW_SUBMISSION', 'submission', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 17:07:01'),
(70, 5, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '2026-01-28 17:21:50'),
(71, 5, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 09:32:56'),
(72, 5, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 10:36:06'),
(73, 5, 'DOWNLOAD_GENERAL_DOCUMENT', 'general_document', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 10:42:34'),
(74, 1, 'LOGIN', 'portal', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 12:26:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `portal_access_tokens`
--

CREATE TABLE `portal_access_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `portal_user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(64) NOT NULL,
  `status` enum('PENDING','USED','REVOKED') NOT NULL DEFAULT 'PENDING',
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `used_ip` varchar(45) DEFAULT NULL,
  `used_user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `portal_access_tokens`
--

INSERT INTO `portal_access_tokens` (`id`, `portal_user_id`, `code`, `status`, `expires_at`, `used_at`, `used_ip`, `used_user_agent`, `created_at`) VALUES
(22, 1, 'EGTAQM7TH5EV', 'REVOKED', '2026-01-07 16:18:34', NULL, NULL, NULL, '2026-01-06 19:18:34'),
(23, 3, 'RSH7ZFZ99FHA', 'REVOKED', '2026-01-08 12:00:42', NULL, NULL, NULL, '2026-01-07 15:00:42'),
(24, 1, 'SXSJTG28D3V3', 'REVOKED', '2026-01-08 12:12:49', NULL, NULL, NULL, '2026-01-07 15:12:49'),
(25, 2, 'EKRFZZ9B4AW2', 'USED', '2026-01-07 13:28:50', '2026-01-07 12:29:26', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-07 15:28:50'),
(26, 1, 'J37J9BGB2ZX4', 'USED', '2026-01-08 12:38:47', '2026-01-07 12:43:27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 15:38:47'),
(27, 3, '9GZRD7UTHX6T', 'USED', '2026-01-08 14:46:06', '2026-01-07 14:46:25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 17:46:06'),
(28, 3, 'J7GBCYCKYTW2', 'USED', '2026-01-14 15:27:00', '2026-01-07 15:27:28', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 18:27:00'),
(29, 3, 'AXF85PHDV9WT', 'USED', '2026-01-08 15:51:19', '2026-01-07 15:51:39', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 18:51:19'),
(30, 3, 'FTUF9W3EW7G5', 'USED', '2026-01-09 10:47:44', '2026-01-08 10:47:50', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-08 13:47:44'),
(31, 3, 'KM8TWT3WGZ2Y', 'USED', '2026-01-09 11:38:33', '2026-01-08 11:38:42', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-08 14:38:33'),
(32, 3, 'XK4H6HGBXWE9', 'USED', '2026-01-13 16:10:19', '2026-01-12 16:10:27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 19:10:19'),
(33, 3, 'G3BW7JAU4ASJ', 'USED', '2026-01-13 16:31:16', '2026-01-12 16:46:54', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 19:31:16'),
(34, 3, 'DDSQU2ACT4ME', 'USED', '2026-01-27 12:00:40', '2026-01-26 12:00:45', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 15:00:40'),
(35, 3, 'KSSF8QW5EN3Q', 'USED', '2026-01-28 10:19:25', '2026-01-27 10:19:28', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 13:19:25'),
(36, 3, 'DYA76V87GWHP', 'USED', '2026-01-28 12:06:05', '2026-01-27 12:06:16', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:06:05'),
(37, 4, 'V49H4RPBS456', 'USED', '2026-01-29 11:01:15', '2026-01-28 11:01:25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 14:01:15'),
(38, 5, '5ZT2U43WQTNR', 'REVOKED', '2026-01-29 14:56:01', NULL, NULL, NULL, '2026-01-28 17:56:01'),
(39, 3, 'X2JW62KF7MNA', 'USED', '2026-01-29 16:16:40', '2026-01-28 16:34:32', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 19:16:40'),
(40, 5, 'YWFTG4BX2FRZ', 'REVOKED', '2026-01-29 17:18:49', NULL, NULL, NULL, '2026-01-28 20:18:49'),
(41, 5, 'KYJWTBA6BDBV', 'REVOKED', '2026-01-29 17:19:29', NULL, NULL, NULL, '2026-01-28 20:19:29'),
(42, 5, 'DA7AJP8EMKMK', 'REVOKED', '2026-01-29 17:19:38', NULL, NULL, NULL, '2026-01-28 20:19:38'),
(43, 5, 'JMC22FC68EZJ', 'REVOKED', '2026-01-29 17:20:52', NULL, NULL, NULL, '2026-01-28 20:20:52'),
(44, 5, 'AU5A7XDYQG48', 'USED', '2026-01-29 17:21:44', '2026-01-28 17:21:50', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '2026-01-28 20:21:44'),
(45, 5, 'TX9JXPVMWEZB', 'USED', '2026-01-30 09:32:52', '2026-01-29 09:32:56', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 12:32:52'),
(46, 1, 'UBRU2BT42UC8', 'USED', '2026-01-30 12:26:23', '2026-01-29 12:26:33', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:26:23'),
(47, 2, '2c25050f53c9f1624509fc23c379042f299d3abe5cfbe275dd33e61c71a8c785', 'REVOKED', '2026-01-30 14:28:27', NULL, NULL, NULL, '2026-01-29 17:28:27'),
(48, 2, '698185b13e46e720191e425caa7b68a8d737dffdbdcbbdc40adcc70ebd0bd24e', 'REVOKED', '2026-01-29 15:54:27', NULL, NULL, NULL, '2026-01-29 17:54:27'),
(49, 2, '1d6bb9236478e0ccb3363d9a0a8a3f8eb6ea413f1bc4f015800e10ee6a83f318', 'PENDING', '2026-01-29 15:54:27', NULL, NULL, NULL, '2026-01-29 17:54:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `portal_announcements`
--

CREATE TABLE `portal_announcements` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `level` enum('info','success','warning','danger') NOT NULL DEFAULT 'info',
  `starts_at` datetime DEFAULT NULL COMMENT 'Data/hora de início da exibição (NULL = imediato)',
  `ends_at` datetime DEFAULT NULL COMMENT 'Data/hora de fim da exibição (NULL = sem fim)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = ativo, 0 = inativo',
  `created_by_admin` int(10) UNSIGNED NOT NULL COMMENT 'ID do admin que criou',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Comunicados e avisos para usuários do portal';

-- --------------------------------------------------------

--
-- Estrutura para tabela `portal_documents`
--

CREATE TABLE `portal_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `portal_user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_original_name` varchar(255) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL,
  `file_mime` varchar(120) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by_admin` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `portal_documents`
--

INSERT INTO `portal_documents` (`id`, `portal_user_id`, `title`, `description`, `file_path`, `file_original_name`, `file_size`, `file_mime`, `created_at`, `created_by_admin`) VALUES
(2, 2, 'Teste', 'vglsdhbvldshfbjdlkfbdfbndzbfzb', 'C:\\xampp\\htdocs\\NimbusDocs/storage/documents/2/\\02eb66fdd4fca35ea96af443e455fe7b.pdf', 'AGT-08.03.2024.pdf', 431720, 'application/pdf', '2026-01-07 10:48:32', 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `portal_submissions`
--

CREATE TABLE `portal_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `portal_user_id` int(10) UNSIGNED NOT NULL,
  `reference_code` varchar(64) NOT NULL,
  `title` varchar(190) NOT NULL,
  `message` text DEFAULT NULL,
  `responsible_name` varchar(190) DEFAULT NULL,
  `company_cnpj` varchar(18) DEFAULT NULL,
  `company_name` varchar(190) DEFAULT NULL,
  `main_activity` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `net_worth` decimal(15,2) DEFAULT NULL,
  `annual_revenue` decimal(15,2) DEFAULT NULL,
  `is_us_person` tinyint(1) DEFAULT 0,
  `is_pep` tinyint(1) DEFAULT 0,
  `shareholder_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`shareholder_data`)),
  `registrant_name` varchar(190) DEFAULT NULL,
  `registrant_position` varchar(100) DEFAULT NULL,
  `registrant_rg` varchar(20) DEFAULT NULL,
  `registrant_cpf` varchar(14) DEFAULT NULL,
  `status` enum('PENDING','UNDER_REVIEW','COMPLETED','REJECTED') NOT NULL DEFAULT 'PENDING',
  `created_ip` varchar(45) DEFAULT NULL,
  `created_user_agent` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_updated_at` datetime DEFAULT NULL,
  `status_updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `portal_submissions`
--

INSERT INTO `portal_submissions` (`id`, `portal_user_id`, `reference_code`, `title`, `message`, `responsible_name`, `company_cnpj`, `company_name`, `main_activity`, `phone`, `website`, `net_worth`, `annual_revenue`, `is_us_person`, `is_pep`, `shareholder_data`, `registrant_name`, `registrant_position`, `registrant_rg`, `registrant_cpf`, `status`, `created_ip`, `created_user_agent`, `submitted_at`, `status_updated_at`, `status_updated_by`) VALUES
(1, 1, 'SUB-20251126-JFN4KB9R', 'fgvdsvsadv', 'svsvsdvsdvavav', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'PENDING', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:16:01', NULL, NULL),
(2, 1, 'SUB-20251126-RBM9CHR6', 'Teste', 'zgvfs\\dbvsdbfdsfbfdazbazdb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'REJECTED', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:18:12', '2026-01-26 12:25:45', 1),
(3, 1, 'SUB-20251201-F3XMXUP7', 'Teste', 'giasbvolidnfblndfblkai.;bsftrjtykdmxfgmkjym', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'COMPLETED', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 18:59:30', '2026-01-26 12:25:09', 1),
(4, 2, 'SUB-20251202-EDEN4J3H', 'Teste', 'asvsdnbvkjdzf kjzdnfk ndzkfbndfzbdfbdzfbdfzbzdfb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'UNDER_REVIEW', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-02 19:44:08', '2026-01-26 12:17:13', 1),
(5, 3, 'SUB-20260107-K7X3N887', 'Cadastro de Cliente', '', 'Isis Tatiane Baptista', '52004807000368', 'MICROSOFT 272945 BRASIL LTDA', 'Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet', '(11) 5504-2155', 'https://www.microsoft.com/pt-br', 1000000000.00, 1000000000000.00, 0, 0, NULL, 'Oliver Lorenzo da Rosa', 'Diretor', '26.069.550-6', '95039350830', 'REJECTED', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 17:54:09', '2026-01-26 12:00:21', 1),
(6, 3, 'SUB-20260107-2DBYB6TN', 'Cadastro de Cliente', '', 'Diego João Souza', '52004807000368', 'MICROSOFT 272945 BRASIL LTDA', 'Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet', '(11) 5504-2155', 'https://www.microsoft.com/pt-br', 1000000000.00, 1000000000.00, 1, 0, NULL, 'Fernando Rodrigo Pietro Mendes', 'Gerente', '22.636.412-4', '23100249887', 'COMPLETED', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 18:20:29', '2026-01-26 11:57:13', 1),
(7, 3, 'SUB-20260126-ZQYBMBSN', 'Cadastro de Cliente', '', 'Diego João Souza', '52004807000368', 'MICROSOFT 272945 BRASIL LTDA', 'Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet', '(11) 5504-2155', '', 1000000000.00, 1000000000.00, 0, 0, NULL, 'Isabel Isadora de Paula', 'Teste', '12.110.273-7', '65195364583', 'REJECTED', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 15:55:23', '2026-01-26 13:00:09', 1),
(8, 3, 'SUB-20260126-DTFBGNU9', 'Cadastro de Cliente', '', 'Alice Sophie Heloisa Duarte', '04712500000107', 'MICROSOFT DO BRASIL IMPORTACAO E COMERCIO DE SOFTWARE E VIDEO GAMES LTDA', 'Desenvolvimento e licenciamento de programas de computador não-customizáveis', '(11) 5504-2155', 'https://www.microsoft.com/pt-br', 1000000000.00, 1000000000.00, 0, 0, NULL, 'Kaique Gustavo Eduardo Gomes', 'Teste', '35.107.366-8', '65837859861', 'COMPLETED', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 16:05:30', '2026-01-28 10:34:19', 1),
(9, 3, 'SUB-20260128-UGQWB2VD', 'Cadastro de Cliente', '', 'Diego Colicchio', '02946761000166', 'RED BULL DO BRASIL LTDA.', 'Comércio atacadista de cerveja, chope e refrigerante', '(11) 3016-2855', 'https://www.redbull.com/', 1000000000.00, 1000000000.00, 0, 0, NULL, 'Benedita Rafaela Nunes', 'Administrador', '27.105.568-6', '27203665283', 'COMPLETED', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 19:44:16', '2026-01-28 16:45:35', 1);

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
(23, 6, 'OTHER', 'ADMIN', 1, 'AGT-08.03.2024.pdf', '17d5bb443d7fe33a185f7ba864435e3b.pdf', 'application/pdf', 431720, '2026/01\\17d5bb443d7fe33a185f7ba864435e3b.pdf', 'ff65496d990b1aba431d4f592fa503c0ff55ffdc47af4f8f45a0b5d1bf4ee8bf', 1, '2026-01-07 18:51:02'),
(24, 7, 'BALANCE_SHEET', 'USER', 0, '1311.pdf', '8526581b8250a4edb10b7b12aa56d55a.pdf', 'application/pdf', 1121860, 'portal_uploads/3/8526581b8250a4edb10b7b12aa56d55a.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 15:55:23'),
(25, 7, 'DRE', 'USER', 0, '1311.pdf', '404d7543666a3f7ea6663a8a19007296.pdf', 'application/pdf', 1121860, 'portal_uploads/3/404d7543666a3f7ea6663a8a19007296.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 15:55:23'),
(26, 7, 'POLICIES', 'USER', 0, '1311.pdf', 'e5d986263e7c2049e97bc89ab03512a6.pdf', 'application/pdf', 1121860, 'portal_uploads/3/e5d986263e7c2049e97bc89ab03512a6.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 15:55:23'),
(27, 7, 'CNPJ_CARD', 'USER', 0, '1311.pdf', '3e1e45baefd7239b45d0af4cdd590c3c.pdf', 'application/pdf', 1121860, 'portal_uploads/3/3e1e45baefd7239b45d0af4cdd590c3c.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 15:55:23'),
(28, 7, 'POWER_OF_ATTORNEY', 'USER', 0, '1311.pdf', '1affa4ef56f9c23e852d08b9f273a906.pdf', 'application/pdf', 1121860, 'portal_uploads/3/1affa4ef56f9c23e852d08b9f273a906.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 15:55:23'),
(29, 7, 'MINUTES', 'USER', 0, '1311.pdf', 'ea48b74ff5f28a58640238f0ffd0d86a.pdf', 'application/pdf', 1121860, 'portal_uploads/3/ea48b74ff5f28a58640238f0ffd0d86a.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 15:55:23'),
(30, 7, 'ARTICLES_OF_INCORPORATION', 'USER', 0, '1311.pdf', '59f9b7c9d927631a24f5191a23305bf3.pdf', 'application/pdf', 1121860, 'portal_uploads/3/59f9b7c9d927631a24f5191a23305bf3.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 15:55:23'),
(31, 7, 'BYLAWS', 'USER', 0, '1311.pdf', '981ac149d54111f26f28d8bc5f7c1c65.pdf', 'application/pdf', 1121860, 'portal_uploads/3/981ac149d54111f26f28d8bc5f7c1c65.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 15:55:23'),
(32, 8, 'BALANCE_SHEET', 'USER', 0, '1311.pdf', '586c7b17d45b3e4d712d17a82ce785c3.pdf', 'application/pdf', 1121860, 'portal_uploads/3/586c7b17d45b3e4d712d17a82ce785c3.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 16:05:30'),
(33, 8, 'DRE', 'USER', 0, '1311.pdf', 'ee2c314a8fcd61d3932309dae16aa804.pdf', 'application/pdf', 1121860, 'portal_uploads/3/ee2c314a8fcd61d3932309dae16aa804.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 16:05:30'),
(34, 8, 'POLICIES', 'USER', 0, '1311.pdf', '40cfb403e13fc557eae8cd067de67266.pdf', 'application/pdf', 1121860, 'portal_uploads/3/40cfb403e13fc557eae8cd067de67266.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 16:05:30'),
(35, 8, 'CNPJ_CARD', 'USER', 0, '1311.pdf', '64b3a1f6543540799b0cf4ffbc51fb66.pdf', 'application/pdf', 1121860, 'portal_uploads/3/64b3a1f6543540799b0cf4ffbc51fb66.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 16:05:30'),
(36, 8, 'POWER_OF_ATTORNEY', 'USER', 0, '1311.pdf', 'f0c7d32b26918d6bcd9d4fda6ed1b6c8.pdf', 'application/pdf', 1121860, 'portal_uploads/3/f0c7d32b26918d6bcd9d4fda6ed1b6c8.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 16:05:30'),
(37, 8, 'MINUTES', 'USER', 0, '1311.pdf', 'ac838a76a044a789284225db620976a1.pdf', 'application/pdf', 1121860, 'portal_uploads/3/ac838a76a044a789284225db620976a1.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 16:05:30'),
(38, 8, 'ARTICLES_OF_INCORPORATION', 'USER', 0, '1311.pdf', 'd5b378541aef02c65d2a4c76203467aa.pdf', 'application/pdf', 1121860, 'portal_uploads/3/d5b378541aef02c65d2a4c76203467aa.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 16:05:30'),
(39, 8, 'BYLAWS', 'USER', 0, '1311.pdf', 'ac25d8a7bc9cb2fd6f2f117b9a990a99.pdf', 'application/pdf', 1121860, 'portal_uploads/3/ac25d8a7bc9cb2fd6f2f117b9a990a99.pdf', '7c56b578fe1bdecb3116235d306b6872688d603e45e88c8a241e3d54d3d09fb4', 1, '2026-01-26 16:05:30'),
(40, 9, 'BALANCE_SHEET', 'USER', 0, 'CVIVA6 2025 07.pdf', '7c82a67b2de418270e6f5fd28568234c.pdf', 'application/pdf', 926758, 'portal_uploads/3/7c82a67b2de418270e6f5fd28568234c.pdf', 'af4a84d946503f4a390253beb94921f208bc8c0242d7ce5a3acc3493f7034a53', 1, '2026-01-28 19:44:16'),
(41, 9, 'DRE', 'USER', 0, 'CVIVA6 2025 07.pdf', '5b8fba19643c8d15c2d70cd43c86475f.pdf', 'application/pdf', 926758, 'portal_uploads/3/5b8fba19643c8d15c2d70cd43c86475f.pdf', 'af4a84d946503f4a390253beb94921f208bc8c0242d7ce5a3acc3493f7034a53', 1, '2026-01-28 19:44:16'),
(42, 9, 'POLICIES', 'USER', 0, 'CVIVA6 2025 07.pdf', 'a274508ac8bea14ec8276c74a37bcec5.pdf', 'application/pdf', 926758, 'portal_uploads/3/a274508ac8bea14ec8276c74a37bcec5.pdf', 'af4a84d946503f4a390253beb94921f208bc8c0242d7ce5a3acc3493f7034a53', 1, '2026-01-28 19:44:16'),
(43, 9, 'CNPJ_CARD', 'USER', 0, 'CVIVA6 2025 07.pdf', 'd1c360dfecba25a136643e55b4373997.pdf', 'application/pdf', 926758, 'portal_uploads/3/d1c360dfecba25a136643e55b4373997.pdf', 'af4a84d946503f4a390253beb94921f208bc8c0242d7ce5a3acc3493f7034a53', 1, '2026-01-28 19:44:16'),
(44, 9, 'POWER_OF_ATTORNEY', 'USER', 0, 'CVIVA6 2025 07.pdf', 'e4ee5aaf3ae256407933b6ae52f86c46.pdf', 'application/pdf', 926758, 'portal_uploads/3/e4ee5aaf3ae256407933b6ae52f86c46.pdf', 'af4a84d946503f4a390253beb94921f208bc8c0242d7ce5a3acc3493f7034a53', 1, '2026-01-28 19:44:16'),
(45, 9, 'MINUTES', 'USER', 0, 'CVIVA6 2025 07.pdf', 'edbdc7a8b6378bc3bd34311015a934ae.pdf', 'application/pdf', 926758, 'portal_uploads/3/edbdc7a8b6378bc3bd34311015a934ae.pdf', 'af4a84d946503f4a390253beb94921f208bc8c0242d7ce5a3acc3493f7034a53', 1, '2026-01-28 19:44:16'),
(46, 9, 'ARTICLES_OF_INCORPORATION', 'USER', 0, 'CVIVA6 2025 07.pdf', '70e8d9c4b4f25dc2f6675c85a397b1ba.pdf', 'application/pdf', 926758, 'portal_uploads/3/70e8d9c4b4f25dc2f6675c85a397b1ba.pdf', 'af4a84d946503f4a390253beb94921f208bc8c0242d7ce5a3acc3493f7034a53', 1, '2026-01-28 19:44:16'),
(47, 9, 'BYLAWS', 'USER', 0, 'CVIVA6 2025 07.pdf', '0f29e03fb036d636b3627b511e620e2c.pdf', 'application/pdf', 926758, 'portal_uploads/3/0f29e03fb036d636b3627b511e620e2c.pdf', 'af4a84d946503f4a390253beb94921f208bc8c0242d7ce5a3acc3493f7034a53', 1, '2026-01-28 19:44:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `portal_submission_file_versions`
--

CREATE TABLE `portal_submission_file_versions` (
  `id` int(10) UNSIGNED NOT NULL,
  `file_id` int(10) UNSIGNED NOT NULL,
  `version` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `storage_path` varchar(255) NOT NULL,
  `size_bytes` bigint(20) UNSIGNED NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `checksum` varchar(128) DEFAULT NULL,
  `uploaded_by_type` enum('ADMIN','PORTAL_USER') NOT NULL,
  `uploaded_by_id` int(10) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `portal_submission_notes`
--

CREATE TABLE `portal_submission_notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `submission_id` int(10) UNSIGNED NOT NULL,
  `admin_user_id` int(10) UNSIGNED DEFAULT NULL,
  `visibility` enum('USER_VISIBLE','ADMIN_ONLY') NOT NULL DEFAULT 'USER_VISIBLE',
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `portal_submission_notes`
--

INSERT INTO `portal_submission_notes` (`id`, `submission_id`, `admin_user_id`, `visibility`, `message`, `created_at`) VALUES
(1, 5, 1, 'USER_VISIBLE', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vel placerat tortor, a molestie elit. Suspendisse in orci at risus pulvinar viverra in at purus. Suspendisse leo odio, condimentum vel risus ac, eleifend tincidunt lectus. Aenean finibus velit nec turpis vestibulum, eget congue sapien malesuada. Suspendisse feugiat dui quis nibh tempus, vel blandit erat tempor. In quis lacinia risus, non aliquam ipsum. Vestibulum quis nulla fermentum, convallis massa ut, dictum velit. Vestibulum consequat risus et quam mattis, id scelerisque nisi varius. Integer vestibulum, nibh et vehicula vehicula, eros massa accumsan odio, et rhoncus lacus massa ut diam. Aliquam quis elit at odio suscipit condimentum. Donec ultrices accumsan tristique. Vivamus sodales dictum magna ac consequat. Nunc sed justo tempus augue tempus vulputate vel pulvinar dui. Vivamus id lacus elit. Sed congue felis sed risus sollicitudin porta. Maecenas fermentum aliquet quam, in ultricies turpis sodales quis.', '2026-01-26 15:00:21'),
(2, 4, 1, 'USER_VISIBLE', 'Vestibulum iaculis dapibus sodales. Vestibulum sit amet nunc urna. Vivamus sagittis tristique nisi, convallis aliquet mauris semper at. Nullam non aliquet magna, vel placerat augue. Donec nec elit tortor. Nam justo lorem, placerat vel ipsum sit amet, consectetur volutpat mauris. Morbi molestie, nulla id varius ullamcorper, lectus urna dapibus arcu, vel aliquam turpis orci sed tellus. Suspendisse interdum mollis convallis. Cras arcu velit, mattis eget ullamcorper in, blandit tincidunt velit.', '2026-01-26 15:17:13'),
(3, 3, 1, 'USER_VISIBLE', 'In mattis magna est, ac faucibus leo elementum et. Sed vestibulum consectetur magna eget vestibulum. Nulla mollis dui dolor, eu tincidunt leo tempus nec. Etiam pulvinar, dui nec iaculis iaculis, est dui vulputate massa, et interdum urna est ac ex. Sed lacinia tellus eget interdum tempor. Proin dignissim eros dapibus maximus ullamcorper. Curabitur dui lacus, commodo ut lacus in, rutrum aliquam mi. Nulla in dui ut neque pulvinar dapibus placerat vitae massa. Nullam eget justo risus. Fusce odio ligula, iaculis sit amet vehicula non, malesuada vitae sapien. Cras gravida elementum finibus. Aenean sit amet venenatis neque.', '2026-01-26 15:25:09'),
(4, 2, 1, 'USER_VISIBLE', 'Aenean consequat euismod metus, sed sollicitudin nibh facilisis vitae. Nam non lacus mauris. Cras vel purus vulputate, laoreet dolor et, condimentum diam. Vestibulum eu metus convallis, tincidunt urna ac, pellentesque ipsum. Nam aliquam bibendum velit, et imperdiet ipsum mattis eu. Ut odio ligula, gravida eu dolor id, convallis dapibus turpis. Duis augue neque, placerat et mattis sit amet, egestas ac nisi. Praesent in felis nec velit elementum bibendum. In et sagittis mi. Suspendisse vel eleifend nulla. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Maecenas eu placerat augue. Fusce tempor laoreet posuere. Nulla ante dui, sollicitudin at purus et, semper commodo velit. Cras dignissim imperdiet sem, in tempus ex hendrerit at.', '2026-01-26 15:25:45'),
(5, 7, 1, 'USER_VISIBLE', 'Nam nec odio ut arcu porta elementum. Aliquam tincidunt nulla at erat placerat, ac egestas lectus aliquam. Morbi nec quam pharetra, ultricies purus id, mattis ante. Aenean id ex diam. Integer euismod pharetra ante condimentum malesuada. Aenean vel ipsum sed ipsum fermentum molestie a eget sem. Aenean ornare libero enim, eget convallis augue rutrum nec. Etiam eu elementum lacus.', '2026-01-26 16:00:09'),
(6, 8, 1, 'USER_VISIBLE', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisi neque, varius non velit eget, iaculis rutrum urna. Fusce dignissim hendrerit viverra. Morbi tincidunt ac leo in tristique. Ut aliquam ante odio. Nunc tortor enim, blandit et massa blandit, semper euismod quam. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer rhoncus tortor a tempor molestie. Suspendisse vel neque est. Cras neque quam, faucibus eu cursus a, sodales sed enim. Cras vestibulum, tortor elementum faucibus pellentesque, metus elit maximus nibh, ac laoreet nisi mauris gravida ipsum. Proin in lorem id tortor auctor sagittis. In faucibus nulla in urna ultricies maximus. Morbi tincidunt rhoncus lectus, consectetur cursus odio pellentesque vel. Suspendisse efficitur, purus in pretium volutpat, ante lacus cursus enim, ac mattis sem neque mollis purus. Pellentesque hendrerit dui eu feugiat suscipit.', '2026-01-28 13:34:19'),
(7, 9, 1, 'USER_VISIBLE', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam mollis, felis rutrum finibus hendrerit, ipsum orci ullamcorper mauris, id lobortis justo leo eget velit. Nullam vel dui justo. Nulla ornare rutrum eros eget ornare. Ut at dui justo. Pellentesque efficitur nisi et neque luctus egestas. Etiam eu justo in turpis rhoncus feugiat vitae et tortor. Aliquam purus nulla, consequat pharetra placerat ut, vulputate et elit. Nam sodales justo et nibh rutrum, id sollicitudin nibh ullamcorper. Nam suscipit ligula et ultrices mattis. Cras blandit nisi sit amet est facilisis egestas. Donec turpis nisl, fermentum quis mi id, malesuada pulvinar dui. Ut porttitor, dolor at vulputate pretium, mauris odio pulvinar sem, vel luctus tellus nunc sed nibh. Pellentesque posuere eget ex et condimentum. Vivamus porta, augue ut pellentesque mattis, elit ex efficitur enim, quis egestas tellus odio ut neque. Integer commodo orci magna, at mollis est mollis nec. Quisque massa risus, maximus id fringilla vitae, bibendum sed neque.', '2026-01-28 19:45:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `portal_submission_shareholders`
--

CREATE TABLE `portal_submission_shareholders` (
  `id` int(10) UNSIGNED NOT NULL,
  `submission_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `document_rg` varchar(20) DEFAULT NULL,
  `document_cnpj` varchar(18) DEFAULT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `portal_submission_shareholders`
--

INSERT INTO `portal_submission_shareholders` (`id`, `submission_id`, `name`, `document_rg`, `document_cnpj`, `percentage`, `created_at`) VALUES
(1, 5, 'Isabelly Caroline Nogueira', '39.592.409-1', '33579067249', 7.33, '2026-01-07 17:54:09'),
(2, 5, 'Rodrigo Heitor Martin dos Santos', '49.121.271-9', '08188001007', 18.47, '2026-01-07 17:54:09'),
(3, 5, 'Eliane Bianca Corte Real', '44.445.980-7', '88668663267', 25.20, '2026-01-07 17:54:09'),
(4, 5, 'Sara Elisa Ribeiro', '28.811.690-2', '77503298626', 49.00, '2026-01-07 17:54:09'),
(5, 6, 'Gabriela Antonella Heloise Aragão', '43.679.869-4', '20477764000180', 7.93, '2026-01-07 18:20:29'),
(6, 6, 'Marcos Noah Severino Assunção', '40.678.964-2', '27917103000177', 16.07, '2026-01-07 18:20:29'),
(7, 6, 'Kamilly Elisa Andrea Gonçalves', '37.720.162-5', '81356080000114', 27.00, '2026-01-07 18:20:29'),
(8, 6, 'Sônia Esther Gomes', '49.519.006-8', '76583699000121', 49.00, '2026-01-07 18:20:29'),
(9, 7, 'Sônia Esther Gomes', '49.519.006-8', '', 49.00, '2026-01-26 15:55:23'),
(10, 7, 'Kamilly Elisa Andrea Gonçalves', '', '81356080000114', 27.00, '2026-01-26 15:55:23'),
(11, 7, 'Marcos Noah Severino Assunção', '40.678.964-2', '', 16.07, '2026-01-26 15:55:23'),
(12, 7, 'Gabriela Antonella Heloise Aragão', '', '20477764000180', 7.93, '2026-01-26 15:55:23'),
(13, 8, 'Benício Edson Gabriel Aparício', '38.497.940-3', '41529086000121', 25.00, '2026-01-26 16:05:30'),
(14, 8, 'Nelson César Oliver da Mota', '36.498.569-0', '53859105000167', 25.00, '2026-01-26 16:05:30'),
(15, 8, 'Renan Kauê Levi Costa', '20.387.947-8', '86129754000161', 25.00, '2026-01-26 16:05:30'),
(16, 8, 'Rita Isadora Cristiane Oliveira', '31.334.943-5', '92287319000168', 25.00, '2026-01-26 16:05:30'),
(17, 9, 'Red Bull Gmbh', '', '05528220000106', 50.00, '2026-01-28 19:44:16'),
(18, 9, 'Red Bull Hangar-7 Gmbh', '', '07136795000109', 50.00, '2026-01-28 19:44:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `portal_users`
--

CREATE TABLE `portal_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(190) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `document_number` varchar(50) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `external_id` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('INVITED','ACTIVE','INACTIVE','BLOCKED') NOT NULL DEFAULT 'INVITED',
  `last_login_at` datetime DEFAULT NULL,
  `last_login_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `portal_users`
--

INSERT INTO `portal_users` (`id`, `full_name`, `email`, `document_number`, `phone_number`, `external_id`, `notes`, `status`, `last_login_at`, `last_login_method`, `created_at`, `updated_at`) VALUES
(1, 'Teste', 'teste@teste.com', '71044965053', '(11) 99999-9999', '', '', 'ACTIVE', '2026-01-29 12:26:33', 'ACCESS_CODE', '2025-11-26 21:14:40', '2026-01-29 15:26:33'),
(2, 'Laís Letícia Malu Rodrigues', 'lais_rodrigues@oi.com.br', '08022819743', '(61) 98661-5844', '', '', 'ACTIVE', '2026-01-07 12:29:26', 'ACCESS_CODE', '2025-12-02 19:26:38', '2026-01-08 18:28:04'),
(3, 'Benjamin Paulo Osvaldo Carvalho', 'benjamin-carvalho85@centerdiesel.com.br', '52059244544', '(96) 99362-4331', '', '', 'ACTIVE', '2026-01-28 16:34:32', 'ACCESS_CODE', '2026-01-07 14:24:52', '2026-01-28 19:34:32'),
(4, 'Luiz Danilo João Pereira', 'luizdanilopereira@velc.com.br', '31757154515', '(66) 98782-2754', '', '', 'ACTIVE', '2026-01-28 11:01:25', 'ACCESS_CODE', '2026-01-28 14:00:38', '2026-01-28 14:01:25'),
(5, 'Maria Eliane Nicole Teixeira', 'andersoncavalcantr96@hotmail.com', '23046005148', '(47) 99334-3927', '', '', 'ACTIVE', '2026-01-29 09:32:56', 'ACCESS_CODE', '2026-01-28 17:48:39', '2026-01-29 12:32:56');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admin_audit_log`
--
ALTER TABLE `admin_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_admin` (`admin_user_id`),
  ADD KEY `idx_audit_action` (`action`),
  ADD KEY `idx_audit_entity` (`entity_type`,`entity_id`);

--
-- Índices de tabela `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_notifications_user` (`admin_user_id`),
  ADD KEY `idx_admin_notifications_unread` (`admin_user_id`,`is_read`);

--
-- Índices de tabela `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_api_tokens_hash` (`token_hash`),
  ADD KEY `idx_api_tokens_user` (`admin_user_id`);

--
-- Índices de tabela `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Índices de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `auth_rate_limits`
--
ALTER TABLE `auth_rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_scope_ip_identifier` (`scope`,`ip`,`identifier`);

--
-- Índices de tabela `document_categories`
--
ALTER TABLE `document_categories`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `general_documents`
--
ALTER TABLE `general_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_general_docs_category` (`category_id`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_migration_filename` (`filename`);

--
-- Índices de tabela `notification_log`
--
ALTER TABLE `notification_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_outbox` (`outbox_id`);

--
-- Índices de tabela `notification_outbox`
--
ALTER TABLE `notification_outbox`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_next` (`status`,`next_attempt_at`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_recipient` (`recipient_email`);

--
-- Índices de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_password_reset_token` (`token`),
  ADD KEY `admin_user_id` (`admin_user_id`),
  ADD KEY `idx_password_reset_expires` (`expires_at`);

--
-- Índices de tabela `portal_access_log`
--
ALTER TABLE `portal_access_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_created_at` (`portal_user_id`,`created_at`),
  ADD KEY `idx_resource` (`resource_type`,`resource_id`);

--
-- Índices de tabela `portal_access_tokens`
--
ALTER TABLE `portal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_portal_access_tokens_code` (`code`),
  ADD KEY `portal_user_id` (`portal_user_id`);

--
-- Índices de tabela `portal_announcements`
--
ALTER TABLE `portal_announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by_admin` (`created_by_admin`),
  ADD KEY `idx_active_dates` (`is_active`,`starts_at`,`ends_at`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Índices de tabela `portal_documents`
--
ALTER TABLE `portal_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `portal_user_id` (`portal_user_id`);

--
-- Índices de tabela `portal_submissions`
--
ALTER TABLE `portal_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `portal_user_id` (`portal_user_id`),
  ADD KEY `status_updated_by` (`status_updated_by`),
  ADD KEY `idx_portal_submissions_status` (`status`);

--
-- Índices de tabela `portal_submission_files`
--
ALTER TABLE `portal_submission_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Índices de tabela `portal_submission_file_versions`
--
ALTER TABLE `portal_submission_file_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_file_versions_file` (`file_id`),
  ADD KEY `idx_file_versions_version` (`file_id`,`version`);

--
-- Índices de tabela `portal_submission_notes`
--
ALTER TABLE `portal_submission_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `admin_user_id` (`admin_user_id`);

--
-- Índices de tabela `portal_submission_shareholders`
--
ALTER TABLE `portal_submission_shareholders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Índices de tabela `portal_users`
--
ALTER TABLE `portal_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_portal_users_email` (`email`),
  ADD UNIQUE KEY `idx_portal_users_document` (`document_number`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admin_audit_log`
--
ALTER TABLE `admin_audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT de tabela `auth_rate_limits`
--
ALTER TABLE `auth_rate_limits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `document_categories`
--
ALTER TABLE `document_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `general_documents`
--
ALTER TABLE `general_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `notification_log`
--
ALTER TABLE `notification_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notification_outbox`
--
ALTER TABLE `notification_outbox`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `portal_access_log`
--
ALTER TABLE `portal_access_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de tabela `portal_access_tokens`
--
ALTER TABLE `portal_access_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de tabela `portal_announcements`
--
ALTER TABLE `portal_announcements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `portal_documents`
--
ALTER TABLE `portal_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `portal_submissions`
--
ALTER TABLE `portal_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `portal_submission_files`
--
ALTER TABLE `portal_submission_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de tabela `portal_submission_file_versions`
--
ALTER TABLE `portal_submission_file_versions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `portal_submission_notes`
--
ALTER TABLE `portal_submission_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `portal_submission_shareholders`
--
ALTER TABLE `portal_submission_shareholders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `portal_users`
--
ALTER TABLE `portal_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD CONSTRAINT `admin_notifications_ibfk_1` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD CONSTRAINT `api_tokens_ibfk_1` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `general_documents`
--
ALTER TABLE `general_documents`
  ADD CONSTRAINT `fk_general_docs_category` FOREIGN KEY (`category_id`) REFERENCES `document_categories` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `notification_log`
--
ALTER TABLE `notification_log`
  ADD CONSTRAINT `fk_notification_log_outbox` FOREIGN KEY (`outbox_id`) REFERENCES `notification_outbox` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `portal_access_log`
--
ALTER TABLE `portal_access_log`
  ADD CONSTRAINT `fk_access_log_portal_user` FOREIGN KEY (`portal_user_id`) REFERENCES `portal_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `portal_access_tokens`
--
ALTER TABLE `portal_access_tokens`
  ADD CONSTRAINT `portal_access_tokens_ibfk_1` FOREIGN KEY (`portal_user_id`) REFERENCES `portal_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `portal_announcements`
--
ALTER TABLE `portal_announcements`
  ADD CONSTRAINT `portal_announcements_ibfk_1` FOREIGN KEY (`created_by_admin`) REFERENCES `admin_users` (`id`);

--
-- Restrições para tabelas `portal_documents`
--
ALTER TABLE `portal_documents`
  ADD CONSTRAINT `fk_portal_documents_user` FOREIGN KEY (`portal_user_id`) REFERENCES `portal_users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `portal_submissions`
--
ALTER TABLE `portal_submissions`
  ADD CONSTRAINT `portal_submissions_ibfk_1` FOREIGN KEY (`portal_user_id`) REFERENCES `portal_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `portal_submissions_ibfk_2` FOREIGN KEY (`status_updated_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `portal_submission_files`
--
ALTER TABLE `portal_submission_files`
  ADD CONSTRAINT `portal_submission_files_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `portal_submissions` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `portal_submission_file_versions`
--
ALTER TABLE `portal_submission_file_versions`
  ADD CONSTRAINT `portal_submission_file_versions_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `portal_submission_files` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `portal_submission_notes`
--
ALTER TABLE `portal_submission_notes`
  ADD CONSTRAINT `portal_submission_notes_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `portal_submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `portal_submission_notes_ibfk_2` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `portal_submission_shareholders`
--
ALTER TABLE `portal_submission_shareholders`
  ADD CONSTRAINT `portal_submission_shareholders_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `portal_submissions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
