-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18/12/2025 às 17:55
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

INSERT INTO `admin_users` (`id`, `name`, `email`, `full_name`, `azure_oid`, `azure_tenant_id`, `azure_upn`, `password_hash`, `auth_mode`, `role`, `is_active`, `status`, `ms_object_id`, `ms_tenant_id`, `ms_upn`, `last_login_at`, `last_login_provider`, `created_at`, `updated_at`) VALUES
(1, 'Anderson Barbosa', 'anderson.cavalcante@bsicapital.com.br', 'Anderson Barbosa', NULL, NULL, NULL, '$2y$10$aXauftJB3ggW.z2oyyZcfu9ghkvjnAxJzYz07dLihoKAdiHdeIb7S', 'LOCAL_ONLY', 'SUPER_ADMIN', 1, 'ACTIVE', NULL, NULL, NULL, '2025-12-18 17:53:46', 'LOCAL', '2025-11-12 14:03:45', '2025-12-18 20:53:46'),
(2, 'Teste', 'teste@bsicapital.com.br', 'Teste', NULL, NULL, NULL, '$2y$10$an4/xFwsFec7YL2BIttNm.4LdSPwwps.Q8E.l/iSXYJi9bSGE.H2G', 'LOCAL_ONLY', 'ADMIN', 0, 'INACTIVE', NULL, NULL, NULL, NULL, NULL, '2025-12-02 19:21:17', '2025-12-18 15:11:11');

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
(1, 'app.name', 'NimbusDocs', '2025-12-16 12:54:04'),
(2, 'app.subtitle', 'Portal seguro de documentos', '2025-12-16 12:54:04'),
(3, 'branding.primary_color', '#00205b', '2025-12-16 12:54:04'),
(4, 'branding.accent_color', '#ffc20e', '2025-12-16 12:54:04'),
(5, 'branding.admin_logo_url', '', '2025-12-16 12:54:04'),
(6, 'branding.portal_logo_url', '', '2025-12-16 12:54:04'),
(7, 'portal.notify.new_submission', '1', '2025-12-02 16:37:49'),
(8, 'portal.notify.status_change', '1', '2025-12-02 16:37:49'),
(9, 'portal.notify.response_upload', '1', '2025-12-02 16:37:49'),
(19, 'notifications.general_documents.enabled', '1', '2025-12-11 17:00:39'),
(20, 'notifications.announcements.enabled', '1', '2025-12-11 17:00:39'),
(21, 'notifications.submission_received.enabled', '1', '2025-12-11 17:00:39'),
(22, 'notifications.submission_status_changed.enabled', '1', '2025-12-11 17:00:39'),
(23, 'notifications.token_created.enabled', '1', '2025-12-11 17:00:39'),
(24, 'notifications.token_expired.enabled', '1', '2025-12-11 17:00:39'),
(25, 'notifications.user_precreated.enabled', '1', '2025-12-11 17:00:39');

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
(53, '2025-12-18 17:53:46', 'ADMIN', 1, NULL, 'LOGIN_SUCCESS', 'ADMIN_USER', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL);

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
(8, '20251217000600_alter_notification_outbox_status.sql', '2025-12-17 19:43:52');

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
(1, 1, 'QTNXMZVK8TA9', 'USED', '2025-11-27 18:15:20', '2025-11-26 18:15:38', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:15:20'),
(2, 1, 'V6PP266V5G8J', 'REVOKED', '2025-11-28 12:47:20', NULL, NULL, NULL, '2025-11-27 15:47:20'),
(3, 1, '27Q7BKEJ6KZS', 'USED', '2025-11-28 12:49:43', '2025-11-27 13:15:24', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-27 15:49:43'),
(4, 1, 'M2UST9TSH3H3', 'REVOKED', '2025-11-29 17:45:44', NULL, NULL, NULL, '2025-11-28 20:45:44'),
(5, 1, 'QS8656YBJVXX', 'USED', '2025-12-02 15:52:39', '2025-12-01 15:52:58', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 18:52:39'),
(6, 1, 'GFTB5JU9NQM4', 'USED', '2025-12-02 15:55:59', '2025-12-01 15:56:03', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 18:55:59'),
(7, 2, 'CKBJ8DSBPMBA', 'REVOKED', '2025-12-03 16:29:05', NULL, NULL, NULL, '2025-12-02 19:29:05'),
(8, 2, '4HZ8X4GFFYFZ', 'REVOKED', '2025-12-03 16:29:10', NULL, NULL, NULL, '2025-12-02 19:29:10'),
(9, 2, 'D4ZXQDET99RH', 'USED', '2025-12-03 16:32:10', '2025-12-02 16:32:51', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-02 19:32:10'),
(10, 2, '43G48RWWPCQB', 'USED', '2025-12-03 16:42:44', '2025-12-02 16:42:50', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-02 19:42:44'),
(11, 2, '6AX5XH29CX77', 'REVOKED', '2025-12-03 17:21:56', NULL, NULL, NULL, '2025-12-02 20:21:56'),
(12, 2, 'APTUGUPKE4VX', 'REVOKED', '2025-12-03 17:22:47', NULL, NULL, NULL, '2025-12-02 20:22:47'),
(13, 2, '8B59PNQR6GET', 'REVOKED', '2025-12-03 17:27:30', NULL, NULL, NULL, '2025-12-02 20:27:30'),
(14, 2, 'JSADKUV5JD3W', 'PENDING', '2025-12-03 17:27:39', NULL, NULL, NULL, '2025-12-02 20:27:39'),
(15, 1, 'AHQ9UJU6CTFV', 'USED', '2025-12-03 17:34:10', '2025-12-02 17:35:25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-02 20:34:10'),
(16, 1, 'SNMMGFKH7Z6Q', 'REVOKED', '2025-12-04 09:58:10', NULL, NULL, NULL, '2025-12-03 12:58:10'),
(17, 1, 'FAAUKB78A5KN', 'REVOKED', '2025-12-04 10:02:26', NULL, NULL, NULL, '2025-12-03 13:02:26'),
(18, 1, '6VKPGFEUF8CJ', 'REVOKED', '2025-12-04 10:04:08', NULL, NULL, NULL, '2025-12-03 13:04:08'),
(19, 1, '3FHEK3EV3XBK', 'REVOKED', '2025-12-04 10:04:11', NULL, NULL, NULL, '2025-12-03 13:04:11'),
(20, 1, 'KRFN3QR5NQYF', 'USED', '2025-12-04 10:04:31', '2025-12-03 10:05:00', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-03 13:04:31'),
(21, 2, 'PBS72VCVEZF9', 'PENDING', '2025-12-11 18:24:29', NULL, NULL, NULL, '2025-12-10 21:24:29');

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
(2, 1, 'SUB-20251126-RBM9CHR6', 'Teste', 'zgvfs\\dbvsdbfdsfbfdazbazdb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'PENDING', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:18:12', NULL, NULL),
(3, 1, 'SUB-20251201-F3XMXUP7', 'Teste', 'giasbvolidnfblndfblkai.;bsftrjtykdmxfgmkjym', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'PENDING', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 18:59:30', NULL, NULL),
(4, 2, 'SUB-20251202-EDEN4J3H', 'Teste', 'asvsdnbvkjdzf kjzdnfk ndzkfbndfzbdfbdzfbdfzbzdfb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'PENDING', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-12-02 19:44:08', NULL, NULL);

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
(6, 4, 'OTHER', 'ADMIN', 1, 'New Portable Document 1.pdf', '61b7f4745d9e4388688c8433be369ed6.pdf', 'application/pdf', 40498, '2025/12/61b7f4745d9e4388688c8433be369ed6.pdf', '44e24611edb45ee6507b2053765bb9525cdf312282d378234dad96a90ccc66de', 1, '2025-12-02 19:44:32');

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
(1, 'Teste', 'teste@teste.com', '71044965053', '11999999999', '', '', 'INVITED', '2025-12-03 10:05:00', 'ACCESS_CODE', '2025-11-26 21:14:40', '2025-12-18 15:11:16'),
(2, 'Laís Letícia Malu Rodrigues', 'lais_rodrigues@oi.com.br', '08022819743', '(61) 98661-5844', '', 'sadbs\\bs\\b\\sdb\\sdb\\sbd', 'INVITED', '2025-12-02 16:42:50', 'ACCESS_CODE', '2025-12-02 19:26:38', '2025-12-02 19:42:50');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de tabela `auth_rate_limits`
--
ALTER TABLE `auth_rate_limits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `document_categories`
--
ALTER TABLE `document_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `general_documents`
--
ALTER TABLE `general_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `notification_log`
--
ALTER TABLE `notification_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notification_outbox`
--
ALTER TABLE `notification_outbox`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `portal_access_log`
--
ALTER TABLE `portal_access_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `portal_access_tokens`
--
ALTER TABLE `portal_access_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `portal_announcements`
--
ALTER TABLE `portal_announcements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `portal_documents`
--
ALTER TABLE `portal_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `portal_submissions`
--
ALTER TABLE `portal_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `portal_submission_files`
--
ALTER TABLE `portal_submission_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `portal_submission_file_versions`
--
ALTER TABLE `portal_submission_file_versions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `portal_submission_notes`
--
ALTER TABLE `portal_submission_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `portal_submission_shareholders`
--
ALTER TABLE `portal_submission_shareholders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `portal_users`
--
ALTER TABLE `portal_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
