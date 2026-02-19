-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping data for table sekai.academic_semesters: ~0 rows (approximately)

-- Dumping data for table sekai.activities: ~0 rows (approximately)

-- Dumping data for table sekai.activity_forms: ~0 rows (approximately)

-- Dumping data for table sekai.activity_presences: ~0 rows (approximately)

-- Dumping data for table sekai.activity_reports: ~0 rows (approximately)

-- Dumping data for table sekai.activity_students: ~0 rows (approximately)

-- Dumping data for table sekai.announcements: ~0 rows (approximately)

-- Dumping data for table sekai.classes: ~3 rows (approximately)
REPLACE INTO `classes` (`id`, `name`, `major_id`, `grade_id`, `capacity`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 'XII RPL 1', 1, 12, 36, NULL, '2026-02-16 17:13:56', '2026-02-16 17:13:56'),
	(2, 'XI RPL 1', 1, 11, 36, NULL, '2026-02-16 18:50:42', '2026-02-16 18:50:42'),
	(3, 'XII TEI 1', 2, 12, 36, NULL, '2026-02-19 03:03:34', '2026-02-19 03:03:34');

-- Dumping data for table sekai.failed_jobs: ~0 rows (approximately)

-- Dumping data for table sekai.grades: ~4 rows (approximately)
REPLACE INTO `grades` (`id`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(10, NULL, NULL, NULL),
	(11, NULL, NULL, NULL),
	(12, NULL, NULL, NULL),
	(13, NULL, NULL, NULL);

-- Dumping data for table sekai.grades_subjects: ~0 rows (approximately)

-- Dumping data for table sekai.lesson_periods: ~0 rows (approximately)

-- Dumping data for table sekai.majors: ~1 rows (approximately)
REPLACE INTO `majors` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 'Rekayasa Perangkat Lunak', NULL, '2026-02-16 17:13:03', '2026-02-16 17:13:03'),
	(2, 'Elektronika', NULL, '2026-02-19 03:03:19', '2026-02-19 03:03:19');

-- Dumping data for table sekai.majors_subjects: ~0 rows (approximately)

-- Dumping data for table sekai.migrations: ~0 rows (approximately)
REPLACE INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_tables', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- Dumping data for table sekai.password_reset_tokens: ~0 rows (approximately)

-- Dumping data for table sekai.personal_access_tokens: ~0 rows (approximately)

-- Dumping data for table sekai.score_distributions: ~0 rows (approximately)

-- Dumping data for table sekai.student_scores: ~0 rows (approximately)

-- Dumping data for table sekai.subjects: ~0 rows (approximately)

-- Dumping data for table sekai.users: ~5 rows (approximately)
REPLACE INTO `users` (`id`, `name`, `email`, `identifier`, `email_verified_at`, `password`, `remember_token`, `role`, `class_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 'Kanade Yoisaki', 'k.nightcord@25.ji', '62626262', NULL, '$2y$10$iS3EXwwWPO0xleQ/8cWYG.oDfxiov5Dy93obSZHvWFVFIrbWdaThe', NULL, 'ADMIN', NULL, NULL, '2026-02-16 17:11:43', '2026-02-18 16:54:04'),
	(2, 'Mafuyu Asahina', 'yuki.nightcord@25.ji', '62626263', NULL, '$2y$10$1BM/LFEoRgQywAOJxrHOe.6bkn1f37mzWqGMFYbZvSVKKa3XQwDDG', NULL, 'VP', NULL, NULL, '2026-02-16 17:14:22', '2026-02-16 17:14:22'),
	(3, 'Mizuki Akiyama', 'amia.nightcord@25.ji', '62626264', NULL, '$2y$10$aka1F/vNZI.niycs9wpGQOC9.BxoZ6XsLDu7hVCWx0kGYLlG8HaJG', NULL, 'TEACHER', NULL, NULL, '2026-02-16 17:15:23', '2026-02-16 17:15:23'),
	(4, 'Ena Shinonome', 'enanan.nightcord@25.ji', '62626265', NULL, '$2y$10$h9Qtfl7o2H3nxxz4z5O/Ne/s2gHP6bAUaTwtB55v1EjRJDlQfdLw2', NULL, 'STUDENT', 1, NULL, '2026-02-16 17:16:10', '2026-02-16 17:16:10'),
	(6, 'Hatsune Miku', 'miku.nightcord@25.ji', '62626266', NULL, '$2y$10$wRwTIWMWgWsZxi4.plKzSewHazI0larT/vX6xSFaa6au5xyNFG7Yq', NULL, 'STUDENT', 3, NULL, '2026-02-16 18:17:46', '2026-02-19 03:07:58');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
