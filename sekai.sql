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
REPLACE INTO `academic_semesters` (`id`, `academic_year`, `semester`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, '2025-2026', 1, NULL, '2026-02-27 03:08:31', '2026-02-27 03:08:31'),
	(2, '2025-2026', 2, NULL, '2026-02-27 03:08:40', '2026-02-27 03:08:40');

-- Dumping data for table sekai.activities: ~0 rows (approximately)
REPLACE INTO `activities` (`id`, `subject_id`, `teacher_id`, `period_id`, `class_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 4, 1, 1, 1, NULL, '2026-02-27 03:11:33', '2026-02-27 03:11:33');

-- Dumping data for table sekai.activity_forms: ~0 rows (approximately)
REPLACE INTO `activity_forms` (`id`, `activity_id`, `activity_date`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 1, '2026-03-02', NULL, '2026-02-27 03:11:59', '2026-02-27 03:11:59');

-- Dumping data for table sekai.activity_presences: ~0 rows (approximately)

-- Dumping data for table sekai.activity_reports: ~0 rows (approximately)

-- Dumping data for table sekai.activity_students: ~0 rows (approximately)
REPLACE INTO `activity_students` (`student_id`, `activity_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(4, 1, NULL, '2026-02-27 22:24:33', '2026-02-27 22:24:33'),
	(6, 1, NULL, '2026-02-27 03:11:33', '2026-02-27 03:11:33');

-- Dumping data for table sekai.announcements: ~0 rows (approximately)
REPLACE INTO `announcements` (`id`, `title`, `subtitle`, `content`, `sender_id`, `scope`, `activity_id`, `grade_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 'Hello world!', 'hello world', 'I really love Kanade. Like, a lot. Like, a whole lot. This is an announcement. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ultricies ultrices risus id varius. Morbi ut semper sem, a malesuada arcu. Integer facilisis, nisl quis vulputate fringilla, ante est sollicitudin diam, eu sodales leo quam maximus augue. Donec ac ultrices neque. Nulla accumsan ipsum a rhoncus sodales. Donec iaculis iaculis dui. Praesent suscipit sapien sapien, at posuere quam vestibulum quis. Fusce at est id orci rhoncus accumsan. Donec et urna ac libero blandit ornare. Maecenas accumsan massa vel sodales maximus. Curabitur ac erat ut nulla feugiat laoreet nec ac nunc. Donec lobortis ac eros at vestibulum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis fringilla, lectus id euismod tincidunt, sem mauris hendrerit erat, sed accumsan risus turpis ut diam. Aliquam vel finibus turpis. Ut id arcu ac elit euismod eleifend id et mi.\r\nInteger facilisis, nisi ut luctus lacinia, lectus nibh placerat ex, sed mollis neque nunc luctus sapien. Maecenas vel mattis sapien. Pellentesque in efficitur massa. Etiam fermentum eu risus non gravida. Vestibulum blandit odio eu convallis mollis. Nunc convallis, magna sed pretium fermentum, velit lectus sagittis magna, nec tincidunt nibh nulla ut nulla. Nunc maximus varius tellus quis volutpat. Donec laoreet metus nec risus lobortis pulvinar. Praesent malesuada vitae augue vel dictum. Sed faucibus pellentesque quam, et pulvinar massa sollicitudin ac. Etiam malesuada commodo ornare. Integer in augue sodales neque gravida ultricies. Cras eros ante, porta ut felis vitae, euismod auctor odio. Ut nec justo in nulla egestas viverra. Nunc hendrerit est at enim rhoncus, eget sollicitudin ipsum facilisis.', 1, 'PUBLIC', NULL, NULL, NULL, '2026-02-27 03:15:30', '2026-02-27 03:15:30');

-- Dumping data for table sekai.classes: ~0 rows (approximately)
REPLACE INTO `classes` (`id`, `name`, `major_id`, `grade_id`, `capacity`, `deleted_at`, `created_at`, `updated_at`, `homeroom_teacher_id`) VALUES
	(1, 'XII RPL 1', 1, 12, 36, NULL, '2026-02-16 17:13:56', '2026-02-16 17:13:56', NULL),
	(2, 'XI RPL 1', 1, 11, 36, NULL, '2026-02-16 18:50:42', '2026-02-16 18:50:42', NULL),
	(3, 'XII TEI 1', 2, 12, 36, NULL, '2026-02-19 03:03:34', '2026-02-19 03:03:34', NULL);

-- Dumping data for table sekai.failed_jobs: ~0 rows (approximately)

-- Dumping data for table sekai.grades: ~0 rows (approximately)
REPLACE INTO `grades` (`id`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(10, NULL, NULL, NULL),
	(11, NULL, NULL, NULL),
	(12, NULL, NULL, NULL),
	(13, NULL, NULL, NULL);

-- Dumping data for table sekai.grades_subjects: ~0 rows (approximately)
REPLACE INTO `grades_subjects` (`grade_id`, `subject_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(10, 1, NULL, '2026-02-27 03:09:18', '2026-02-27 03:09:18'),
	(10, 2, NULL, '2026-02-27 03:09:35', '2026-02-27 03:09:35'),
	(10, 4, NULL, '2026-02-27 03:10:16', '2026-02-27 03:10:16'),
	(11, 3, NULL, '2026-02-27 03:09:54', '2026-02-27 03:09:54'),
	(11, 4, NULL, '2026-02-27 03:10:16', '2026-02-27 03:10:16'),
	(12, 3, NULL, '2026-02-27 03:09:54', '2026-02-27 03:09:54'),
	(12, 4, NULL, '2026-02-27 03:10:16', '2026-02-27 03:10:16'),
	(13, 3, NULL, '2026-02-27 03:09:54', '2026-02-27 03:09:54');

-- Dumping data for table sekai.lesson_periods: ~0 rows (approximately)
REPLACE INTO `lesson_periods` (`id`, `weekday`, `time_begin`, `time_end`, `semester_id`, `parent_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 0, '01:00:00', '02:00:00', 1, NULL, NULL, '2026-02-27 03:10:48', '2026-02-27 03:10:48'),
	(2, 1, '01:00:00', '02:00:00', 1, 1, NULL, '2026-02-27 03:10:48', '2026-02-27 03:10:48'),
	(3, 2, '01:00:00', '02:00:00', 1, 1, NULL, '2026-02-27 03:10:48', '2026-02-27 03:10:48'),
	(4, 3, '01:00:00', '02:00:00', 1, 1, NULL, '2026-02-27 03:10:48', '2026-02-27 03:10:48'),
	(5, 4, '01:00:00', '02:00:00', 1, 1, NULL, '2026-02-27 03:10:48', '2026-02-27 03:10:48'),
	(6, 5, '01:00:00', '02:00:00', 1, 1, NULL, '2026-02-27 03:10:48', '2026-02-27 03:10:48'),
	(7, 6, '01:00:00', '02:00:00', 1, 1, NULL, '2026-02-27 03:10:48', '2026-02-27 03:10:48');

-- Dumping data for table sekai.majors: ~0 rows (approximately)
REPLACE INTO `majors` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 'Rekayasa Perangkat Lunak', NULL, '2026-02-16 17:13:03', '2026-02-16 17:13:03'),
	(2, 'Elektronika', NULL, '2026-02-19 03:03:19', '2026-02-19 03:03:19');

-- Dumping data for table sekai.majors_subjects: ~0 rows (approximately)
REPLACE INTO `majors_subjects` (`major_id`, `subject_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, '2026-02-27 03:09:18', '2026-02-27 03:09:18'),
	(1, 2, NULL, '2026-02-27 03:09:35', '2026-02-27 03:09:35'),
	(1, 3, NULL, '2026-02-27 03:09:54', '2026-02-27 03:09:54'),
	(1, 4, NULL, '2026-02-27 03:10:16', '2026-02-27 03:10:16'),
	(2, 1, NULL, '2026-02-27 03:09:18', '2026-02-27 03:09:18'),
	(2, 2, NULL, '2026-02-27 03:09:35', '2026-02-27 03:09:35'),
	(2, 3, NULL, '2026-02-27 03:09:54', '2026-02-27 03:09:54');

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
REPLACE INTO `subjects` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 'Arts', NULL, '2026-02-27 03:09:18', '2026-02-27 03:09:18'),
	(2, 'Vocational Basics', NULL, '2026-02-27 03:09:35', '2026-02-27 03:09:35'),
	(3, 'Advanced Vocational Studies', NULL, '2026-02-27 03:09:54', '2026-02-27 03:09:54'),
	(4, 'Game Development', NULL, '2026-02-27 03:10:16', '2026-02-27 03:10:16');

-- Dumping data for table sekai.users: ~0 rows (approximately)
REPLACE INTO `users` (`id`, `name`, `email`, `identifier`, `email_verified_at`, `password`, `remember_token`, `role`, `class_id`, `student_order`, `deleted_at`, `created_at`, `updated_at`) VALUES
	(1, 'Kanade Yoisaki', 'k.nightcord@25.ji', '62626262', NULL, '$2y$10$iS3EXwwWPO0xleQ/8cWYG.oDfxiov5Dy93obSZHvWFVFIrbWdaThe', NULL, 'ADMIN', 1, NULL, NULL, '2026-02-16 17:11:43', '2026-02-19 19:31:28'),
	(2, 'Mafuyu Asahina', 'yuki.nightcord@25.ji', '62626263', NULL, '$2y$10$1BM/LFEoRgQywAOJxrHOe.6bkn1f37mzWqGMFYbZvSVKKa3XQwDDG', NULL, 'VP', NULL, NULL, NULL, '2026-02-16 17:14:22', '2026-02-19 19:31:28'),
	(3, 'Mizuki Akiyama', 'amia.nightcord@25.ji', '62626264', NULL, '$2y$10$aka1F/vNZI.niycs9wpGQOC9.BxoZ6XsLDu7hVCWx0kGYLlG8HaJG', NULL, 'TEACHER', NULL, NULL, NULL, '2026-02-16 17:15:23', '2026-02-19 19:29:40'),
	(4, 'Ena Shinonome', 'enanan.nightcord@25.ji', '62626265', NULL, '$2y$10$h9Qtfl7o2H3nxxz4z5O/Ne/s2gHP6bAUaTwtB55v1EjRJDlQfdLw2', NULL, 'STUDENT', 1, 1, NULL, '2026-02-16 17:16:10', '2026-02-27 22:24:33'),
	(6, 'Hatsune Miku', 'miku.nightcord@25.ji', '62626266', NULL, '$2y$10$wRwTIWMWgWsZxi4.plKzSewHazI0larT/vX6xSFaa6au5xyNFG7Yq', NULL, 'STUDENT', 1, NULL, NULL, '2026-02-16 18:17:46', '2026-02-20 20:59:34');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
