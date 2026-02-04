CREATE TABLE `jobs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue` varchar(255) NOT NULL DEFAULT 'default',
    `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
    `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
    `max_attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
    `reserved_at` datetime DEFAULT NULL,
    `available_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_jobs_queue` (`queue`),
    KEY `idx_jobs_status` (`queue`, `reserved_at`, `available_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
