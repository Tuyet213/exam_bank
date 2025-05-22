-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 22, 2025 lúc 01:45 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `exam_bank`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bac_dao_taos`
--

CREATE TABLE `bac_dao_taos` (
  `id` varchar(6) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bac_dao_taos`
--

INSERT INTO `bac_dao_taos` (`id`, `ten`, `able`, `created_at`, `updated_at`) VALUES
('CNTT63', 'Công nghệ thông tin (K63)', 1, '2025-05-06 17:21:03', '2025-05-06 17:21:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bien_ban_hops`
--

CREATE TABLE `bien_ban_hops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_ct_ds_dang_ky` bigint(20) UNSIGNED DEFAULT NULL,
  `thoi_gian` datetime NOT NULL,
  `noi_dung` text DEFAULT NULL,
  `dia_diem` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cap` varchar(255) DEFAULT NULL,
  `id_ds_dang_ky` bigint(20) UNSIGNED DEFAULT NULL,
  `trang_thai` varchar(255) NOT NULL DEFAULT 'Draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bien_ban_hops`
--

INSERT INTO `bien_ban_hops` (`id`, `id_ct_ds_dang_ky`, `thoi_gian`, `noi_dung`, `dia_diem`, `able`, `created_at`, `updated_at`, `cap`, `id_ds_dang_ky`, `trang_thai`) VALUES
(1, 1, '2025-05-09 00:50:00', NULL, 'G6.101', 1, '2025-05-06 17:51:21', '2025-05-06 18:11:22', 'Bộ môn', NULL, 'Draft'),
(2, 2, '2025-05-03 00:51:00', NULL, 'G6.102', 1, '2025-05-06 17:51:21', '2025-05-06 17:51:21', 'Bộ môn', NULL, 'Draft');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bo_mons`
--

CREATE TABLE `bo_mons` (
  `id` varchar(6) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `id_khoa` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bo_mons`
--

INSERT INTO `bo_mons` (`id`, `ten`, `able`, `id_khoa`, `created_at`, `updated_at`) VALUES
('admin', 'Admin', 0, 'admin', NULL, NULL),
('CNPM', 'Công nghệ phần mềm', 1, 'CNTT', NULL, NULL),
('dbcl', 'Đảm bảo chất lượng', 0, 'DBCL', NULL, NULL),
('KTTT', 'Kỹ thuật tàu thủy', 1, 'KTGT', '2025-05-06 17:20:10', '2025-05-06 17:20:10'),
('MMT', 'Mạng máy tính', 1, 'CNTT', '2025-05-06 17:20:29', '2025-05-06 17:20:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('spatie.permission.cache', 'a:3:{s:5:\"alias\";a:3:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";}s:11:\"permissions\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:38:\"Tạo ngân hàng câu hỏi/đề thi\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:16:\"Xuất đề thi\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:29:\"Duyệt danh sách đăng ký\";s:1:\"c\";s:3:\"web\";}}s:5:\"roles\";a:0:{}}', 1747981324);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cau_hois`
--

CREATE TABLE `cau_hois` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cau_hoi` text NOT NULL,
  `muc_do` varchar(255) DEFAULT NULL,
  `id_ct_ds_dang_ky` bigint(20) UNSIGNED NOT NULL,
  `id_chuan_dau_ra` bigint(20) UNSIGNED NOT NULL,
  `id_chuong` bigint(20) UNSIGNED NOT NULL,
  `phan_loai` varchar(255) NOT NULL,
  `diem` double NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cau_hois`
--

INSERT INTO `cau_hois` (`id`, `cau_hoi`, `muc_do`, `id_ct_ds_dang_ky`, `id_chuan_dau_ra`, `id_chuong`, `phan_loai`, `diem`, `able`, `created_at`, `updated_at`) VALUES
(26, 'nnnnnnnnnnn', '2', 2, 4, 4, '1', 0.55, 1, '2025-05-10 06:36:42', '2025-05-10 07:14:45'),
(31, 'ccccccccccccccc', '1', 2, 3, 3, '1', 0.75, 1, '2025-05-10 06:48:59', '2025-05-10 06:48:59'),
(42, '333333333333333333', '2', 2, 4, 4, '1', 1, 1, '2025-05-10 12:10:51', '2025-05-10 12:10:51'),
(43, '333333333333333333', '2', 2, 4, 4, '1', 1, 1, '2025-05-10 12:11:08', '2025-05-10 12:11:08'),
(44, '11111111111111111111', '2', 2, 3, 3, '1', 0.5, 1, '2025-05-10 12:15:41', '2025-05-10 12:15:41'),
(45, '2222222222222222222', '2', 2, 3, 3, '1', 1, 1, '2025-05-10 12:15:41', '2025-05-10 12:15:41'),
(46, '333333333333333333', '2', 2, 4, 4, '1', 1, 1, '2025-05-10 12:15:41', '2025-05-10 12:15:41'),
(47, '11111111111111111111', '2', 2, 3, 3, '1', 0.5, 1, '2025-05-10 12:23:43', '2025-05-10 12:23:43'),
(48, '2222222222222222222', '2', 2, 3, 3, '1', 1, 1, '2025-05-10 12:23:44', '2025-05-10 12:23:44'),
(49, '333333333333333333', '2', 2, 4, 4, '1', 1, 1, '2025-05-10 12:23:44', '2025-05-10 12:23:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuan_dau_ras`
--

CREATE TABLE `chuan_dau_ras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten` varchar(255) NOT NULL,
  `noi_dung` text NOT NULL,
  `id_hoc_phan` varchar(6) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chuan_dau_ras`
--

INSERT INTO `chuan_dau_ras` (`id`, `ten`, `noi_dung`, `id_hoc_phan`, `able`, `created_at`, `updated_at`) VALUES
(3, 'c', 'cccccccccccccc', 'SOT555', 1, '2025-05-06 17:26:05', '2025-05-06 17:26:05'),
(4, 'd', 'ddddddđ', 'SOT555', 1, '2025-05-06 17:26:05', '2025-05-06 17:26:05'),
(5, 'a', 'aaaaaaaaa', 'SOT111', 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(6, 'b', 'bbbbbbbbb', 'SOT111', 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(7, 'c', 'cccccccccccccccc', 'SOT111', 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(8, 'd', 'ddddddddddddd', 'SOT111', 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuc_vus`
--

CREATE TABLE `chuc_vus` (
  `id` varchar(6) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chuc_vus`
--

INSERT INTO `chuc_vus` (`id`, `ten`, `able`, `created_at`, `updated_at`) VALUES
('admin', 'Admin', 1, NULL, NULL),
('gv', 'Giảng viên', 1, NULL, NULL),
('nvdbcl', 'Nhân viên phòng đảm bảo chất lượng', 1, NULL, NULL),
('tbm', 'Trưởng bộ môn', 1, NULL, NULL),
('tk', 'Trưởng khoa', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuongs`
--

CREATE TABLE `chuongs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten` varchar(255) NOT NULL,
  `id_hoc_phan` varchar(6) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chuongs`
--

INSERT INTO `chuongs` (`id`, `ten`, `id_hoc_phan`, `able`, `created_at`, `updated_at`) VALUES
(3, '1', 'SOT555', 1, '2025-05-06 17:26:05', '2025-05-06 17:26:05'),
(4, '2', 'SOT555', 1, '2025-05-06 17:26:05', '2025-05-06 17:26:05'),
(5, '1', 'SOT111', 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(6, '2', 'SOT111', 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(7, '3', 'SOT111', 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(8, '4', 'SOT111', 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuong_chuan_dau_ras`
--

CREATE TABLE `chuong_chuan_dau_ras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_chuong` bigint(20) UNSIGNED NOT NULL,
  `id_chuan_dau_ra` bigint(20) UNSIGNED NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chuong_chuan_dau_ras`
--

INSERT INTO `chuong_chuan_dau_ras` (`id`, `id_chuong`, `id_chuan_dau_ra`, `able`, `created_at`, `updated_at`) VALUES
(4, 3, 3, 1, '2025-05-06 17:26:05', '2025-05-06 17:26:05'),
(5, 4, 4, 1, '2025-05-06 17:26:05', '2025-05-06 17:26:05'),
(6, 5, 5, 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(7, 5, 6, 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(8, 6, 6, 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(9, 7, 5, 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(10, 7, 8, 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(11, 8, 7, 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00'),
(12, 8, 8, 1, '2025-05-10 09:06:00', '2025-05-10 09:06:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `c_t_de_this`
--

CREATE TABLE `c_t_de_this` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_de_thi` bigint(20) UNSIGNED NOT NULL,
  `id_cau_hoi` bigint(20) UNSIGNED NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `c_t_d_s_dang_kies`
--

CREATE TABLE `c_t_d_s_dang_kies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_ds_dang_ky` bigint(20) UNSIGNED NOT NULL,
  `id_hoc_phan` varchar(6) NOT NULL,
  `loai_ngan_hang` tinyint(1) NOT NULL DEFAULT 0,
  `hinh_thuc_thi` varchar(255) DEFAULT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 0,
  `trang_thai` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `c_t_d_s_dang_kies`
--

INSERT INTO `c_t_d_s_dang_kies` (`id`, `id_ds_dang_ky`, `id_hoc_phan`, `loai_ngan_hang`, `hinh_thuc_thi`, `so_luong`, `trang_thai`, `able`, `created_at`, `updated_at`) VALUES
(1, 1, 'SOT111', 1, 'Trắc nghiệm', 150, 'Approved', 1, '2025-05-06 17:27:00', '2025-05-06 17:30:47'),
(2, 1, 'SOT555', 1, 'Tự luận', 100, 'Approved', 1, '2025-05-06 17:27:00', '2025-05-06 17:34:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dap_ans`
--

CREATE TABLE `dap_ans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_cau_hoi` bigint(20) UNSIGNED NOT NULL,
  `dap_an` varchar(255) NOT NULL,
  `diem` decimal(4,2) NOT NULL DEFAULT 0.00,
  `trang_thai` tinyint(1) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dap_ans`
--

INSERT INTO `dap_ans` (`id`, `id_cau_hoi`, `dap_an`, `diem`, `trang_thai`, `able`, `created_at`, `updated_at`) VALUES
(52, 26, 'nnnnnnn', 0.25, 1, 1, '2025-05-10 06:36:42', '2025-05-10 07:14:45'),
(61, 31, 'cccccccccccc', 0.25, 1, 1, '2025-05-10 06:48:59', '2025-05-10 06:48:59'),
(62, 31, 'yyyyyyyyyyyyyyyy', 0.25, 1, 1, '2025-05-10 06:48:59', '2025-05-10 06:48:59'),
(63, 31, '. yyyyyyyyyyyyyyyy', 0.25, 1, 1, '2025-05-10 06:48:59', '2025-05-10 06:48:59'),
(66, 26, 'bbbbbbbbbbb', 0.30, 1, 1, '2025-05-10 07:13:20', '2025-05-10 07:14:45'),
(85, 42, '333333333333333333', 0.50, 1, 1, '2025-05-10 12:10:51', '2025-05-10 12:10:51'),
(86, 43, '333333333333333333', 0.50, 1, 1, '2025-05-10 12:11:08', '2025-05-10 12:11:08'),
(87, 44, '111111111111', 0.25, 1, 1, '2025-05-10 12:15:41', '2025-05-10 12:15:41'),
(88, 44, '11111111111111', 0.25, 1, 1, '2025-05-10 12:15:41', '2025-05-10 12:15:41'),
(89, 45, '2222222222222222222', 0.50, 1, 1, '2025-05-10 12:15:41', '2025-05-10 12:15:41'),
(90, 45, '2222222222222222222', 0.25, 1, 1, '2025-05-10 12:15:41', '2025-05-10 12:15:41'),
(91, 45, '2222222222222222222', 0.25, 1, 1, '2025-05-10 12:15:41', '2025-05-10 12:15:41'),
(92, 46, '333333333333333333', 0.50, 1, 1, '2025-05-10 12:15:41', '2025-05-10 12:15:41'),
(93, 47, '111111111111', 0.25, 1, 1, '2025-05-10 12:23:44', '2025-05-10 12:23:44'),
(94, 47, '11111111111111', 0.25, 1, 1, '2025-05-10 12:23:44', '2025-05-10 12:23:44'),
(95, 48, '2222222222222222222', 0.50, 1, 1, '2025-05-10 12:23:44', '2025-05-10 12:23:44'),
(96, 48, '2222222222222222222', 0.25, 1, 1, '2025-05-10 12:23:44', '2025-05-10 12:23:44'),
(97, 48, '2222222222222222222', 0.25, 1, 1, '2025-05-10 12:23:44', '2025-05-10 12:23:44'),
(98, 49, '333333333333333333', 0.50, 1, 1, '2025-05-10 12:23:44', '2025-05-10 12:23:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `de_this`
--

CREATE TABLE `de_this` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_hoc_phan` varchar(6) NOT NULL,
  `id_lop_hoc_phan` bigint(20) UNSIGNED NOT NULL,
  `hoc_ky` tinyint(4) NOT NULL,
  `nam` smallint(6) NOT NULL,
  `ngay_thi` date NOT NULL,
  `loai` varchar(255) NOT NULL,
  `su_dung_tai_lieu` tinyint(1) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `d_s_dang_kies`
--

CREATE TABLE `d_s_dang_kies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_bo_mon` varchar(6) NOT NULL,
  `hoc_ki` varchar(255) DEFAULT NULL,
  `nam_hoc` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `d_s_dang_kies`
--

INSERT INTO `d_s_dang_kies` (`id`, `id_bo_mon`, `hoc_ki`, `nam_hoc`, `able`, `created_at`, `updated_at`) VALUES
(1, 'CNPM', '2', '2024-2025', 1, '2025-05-06 17:27:00', '2025-05-06 17:27:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `d_s_g_v_bien_soans`
--

CREATE TABLE `d_s_g_v_bien_soans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_ct_ds_dang_ky` bigint(20) UNSIGNED NOT NULL,
  `id_vien_chuc` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `so_gio` double NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `d_s_g_v_bien_soans`
--

INSERT INTO `d_s_g_v_bien_soans` (`id`, `id_ct_ds_dang_ky`, `id_vien_chuc`, `created_at`, `updated_at`, `so_gio`) VALUES
(1, 1, 'GV001', '2025-05-06 17:27:00', '2025-05-06 18:12:17', 15),
(2, 1, 'GV002', '2025-05-06 17:27:00', '2025-05-06 18:12:17', 15),
(3, 2, 'GV001', '2025-05-06 17:27:00', '2025-05-06 17:27:00', 0),
(4, 2, 'GV003', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `d_s_hops`
--

CREATE TABLE `d_s_hops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_bien_ban_hop` bigint(20) UNSIGNED NOT NULL,
  `id_nhiem_vu` bigint(20) UNSIGNED NOT NULL,
  `id_vien_chuc` varchar(6) NOT NULL,
  `so_gio` decimal(8,2) DEFAULT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `d_s_hops`
--

INSERT INTO `d_s_hops` (`id`, `id_bien_ban_hop`, `id_nhiem_vu`, `id_vien_chuc`, `so_gio`, `able`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'GV001', 2.00, 1, '2025-05-06 17:51:21', '2025-05-06 18:12:17'),
(2, 1, 2, 'GV003', 3.00, 1, '2025-05-06 17:51:21', '2025-05-06 18:12:17'),
(3, 1, 3, 'GV001', 4.00, 1, '2025-05-06 17:51:21', '2025-05-06 18:12:17'),
(4, 1, 3, 'GV002', 5.00, 1, '2025-05-06 17:51:21', '2025-05-06 18:12:17'),
(5, 1, 4, 'NV001', 6.00, 1, '2025-05-06 17:51:21', '2025-05-06 18:12:17'),
(6, 2, 1, 'GV001', NULL, 1, '2025-05-06 17:51:21', '2025-05-06 17:51:21'),
(7, 2, 2, 'GV001', NULL, 1, '2025-05-06 17:51:21', '2025-05-06 17:51:21'),
(8, 2, 3, 'GV003', NULL, 1, '2025-05-06 17:51:21', '2025-05-06 17:51:21'),
(9, 2, 3, 'GV003', NULL, 1, '2025-05-06 17:51:21', '2025-05-06 17:51:21'),
(10, 2, 4, 'NV001', NULL, 1, '2025-05-06 17:51:21', '2025-05-06 17:51:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_quy_dois`
--

CREATE TABLE `gio_quy_dois` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gio` tinyint(4) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `loai_de_thi` varchar(255) DEFAULT NULL,
  `loai_hanh_dong` varchar(255) DEFAULT NULL,
  `so_luong` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `gio_quy_dois`
--

INSERT INTO `gio_quy_dois` (`id`, `gio`, `able`, `created_at`, `updated_at`, `loai_de_thi`, `loai_hanh_dong`, `so_luong`) VALUES
(1, 3, 1, '2025-05-06 17:22:33', '2025-05-06 17:22:33', '0', '0', 15),
(2, 1, 1, '2025-05-06 17:22:55', '2025-05-06 17:22:55', '1', '0', 2),
(3, 2, 1, '2025-05-06 17:23:07', '2025-05-06 17:23:07', '0', '1', 15),
(4, 2, 1, '2025-05-06 17:23:22', '2025-05-06 17:23:22', '1', '1', 2),
(5, 1, 1, '2025-05-12 07:41:36', '2025-05-12 07:42:37', '0', '1', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoc_phans`
--

CREATE TABLE `hoc_phans` (
  `id` varchar(6) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `so_tin_chi` tinyint(3) UNSIGNED NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `id_bo_mon` varchar(6) NOT NULL,
  `id_bac_dao_tao` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hoc_phans`
--

INSERT INTO `hoc_phans` (`id`, `ten`, `so_tin_chi`, `able`, `id_bo_mon`, `id_bac_dao_tao`, `created_at`, `updated_at`) VALUES
('SOT111', 'Nhập môn lập trình', 3, 1, 'CNPM', 'CNTT63', '2025-05-06 17:22:03', '2025-05-06 17:22:03'),
('SOT555', 'Kỹ thuật lập trình', 3, 1, 'CNPM', 'CNTT63', '2025-05-06 17:26:05', '2025-05-06 17:26:05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoas`
--

CREATE TABLE `khoas` (
  `id` varchar(6) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khoas`
--

INSERT INTO `khoas` (`id`, `ten`, `able`, `created_at`, `updated_at`) VALUES
('admin', 'Admin', 0, NULL, NULL),
('CNTT', 'Công nghệ thông tin', 1, NULL, NULL),
('DBCL', 'Đảm bảo chất lượng', 0, NULL, NULL),
('KTGT', 'Kỹ thuật giao thông', 1, '2025-05-06 17:19:46', '2025-05-06 17:19:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lop_hoc_phans`
--

CREATE TABLE `lop_hoc_phans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten` varchar(255) NOT NULL,
  `ky_hoc` varchar(255) NOT NULL,
  `nam_hoc` varchar(255) NOT NULL,
  `id_khoa` varchar(6) NOT NULL,
  `id_hoc_phan` varchar(6) NOT NULL,
  `id_vien_chuc` varchar(6) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ma_trans`
--

CREATE TABLE `ma_trans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_chuan_dau_ra` bigint(20) UNSIGNED NOT NULL,
  `id_chuong` bigint(20) UNSIGNED NOT NULL,
  `diem` double NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `so_cau_de` int(11) NOT NULL DEFAULT 0,
  `so_cau_tb` int(11) NOT NULL DEFAULT 0,
  `so_cau_kho` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ma_trans`
--

INSERT INTO `ma_trans` (`id`, `id_chuan_dau_ra`, `id_chuong`, `diem`, `able`, `created_at`, `updated_at`, `so_cau_de`, `so_cau_tb`, `so_cau_kho`) VALUES
(8, 5, 5, 0, 1, '2025-05-10 10:25:22', '2025-05-10 10:25:22', 1, 1, 1),
(9, 6, 5, 0, 1, '2025-05-10 10:25:22', '2025-05-10 10:29:34', 0, 1, 0),
(10, 6, 6, 0, 1, '2025-05-10 10:25:22', '2025-05-10 10:25:22', 0, 0, 0),
(11, 5, 7, 0, 1, '2025-05-10 10:25:22', '2025-05-10 10:25:22', 0, 0, 0),
(12, 8, 7, 0, 1, '2025-05-10 10:25:22', '2025-05-10 10:29:34', 0, 1, 0),
(13, 7, 8, 0, 1, '2025-05-10 10:25:22', '2025-05-10 10:25:22', 1, 1, 0),
(14, 8, 8, 0, 1, '2025-05-10 10:25:22', '2025-05-10 10:25:22', 0, 0, 0),
(15, 3, 3, 0, 1, '2025-05-10 10:30:57', '2025-05-10 10:30:57', 1, 0, 0),
(16, 4, 4, 0, 1, '2025-05-10 10:30:57', '2025-05-10 10:30:57', 0, 1, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_02_18_031743_create_khoas_table', 1),
(2, '2025_02_18_031910_create_bo_mons_table', 1),
(3, '2025_02_18_031945_create_chuc_vus_table', 1),
(4, '2025_02_18_031956_create_bac_dao_taos_table', 1),
(5, '2025_02_18_032007_create_hoc_phans_table', 1),
(6, '2025_02_18_032014_create_chuongs_table', 1),
(7, '2025_02_18_032024_create_chuan_dau_ras_table', 1),
(8, '2025_02_18_032045_create_ma_trans_table', 1),
(9, '2025_02_18_032100_create_d_s_dang_kies_table', 1),
(10, '2025_02_18_032107_create_gio_quy_dois_table', 1),
(11, '2025_02_18_032108_create_cache_table', 1),
(12, '2025_02_18_032108_create_jobs_table', 1),
(13, '2025_02_18_032108_create_permission_tables', 1),
(14, '2025_02_18_032108_create_users_table', 1),
(15, '2025_02_18_032109_create_c_t_d_s_dang_kies_table', 1),
(16, '2025_02_18_032114_create_lops_table', 1),
(17, '2025_02_18_032126_create_cau_hois_table', 1),
(18, '2025_02_18_032133_create_dap_ans_table', 1),
(19, '2025_02_18_032139_create_de_this_table', 1),
(20, '2025_02_18_032147_create_c_t_de_this_table', 1),
(21, '2025_02_18_032156_create_nhiem_vus_table', 1),
(22, '2025_02_18_032203_create_bien_ban_hops_table', 1),
(23, '2025_02_18_032210_create_d_s_hops_table', 1),
(24, '2025_02_22_072954_create_chuong_chuan_dau_ras_table', 1),
(25, '2025_03_05_161619_add_able_to_user', 1),
(26, '2025_03_06_161650_add_timestamps_to_chuandaura', 1),
(27, '2025_03_10_075020_add_cap_column_into_bienbanhop', 1),
(28, '2025_03_10_075207_remove_hocphi_from_hocphan', 1),
(29, '2025_03_10_075354_revise_gio_qui_doi', 1),
(30, '2025_03_12_100527_rm_sluong_from_lophocphans', 1),
(31, '2025_03_23_172433_create_table_thong_baos', 1),
(32, '2025_03_23_172913_update_table_thong_baos', 1),
(33, '2025_03_29_150653_modify_model_id_to_string', 1),
(34, '2025_03_30_194612_update_sessions_table_user_id_type', 1),
(35, '2025_04_03_162619_add_ten_column_to_d_s_dang_kies_table', 1),
(36, '2025_04_13_110946_add_hoc_ki_to_d_s_dang_kies_table', 1),
(37, '2025_04_13_111122_remove_ten_from_d_s_dang_kies_table', 1),
(38, '2025_04_13_160025_add_loai_ngan_hang_and_so_luong_to_c_t_d_s_dang_kies_table', 1),
(39, '2025_04_13_160036_add_fields_to_ctdsdangky', 1),
(40, '2025_04_14_011703_modify_d_s_dang_kies_table', 1),
(41, '2025_04_16_121211_update_bien_ban_hops_table_allow_null_fields', 1),
(42, '2025_04_16_122043_update_d_s_hops_table_allow_null_so_gio', 1),
(43, '2025_04_17_073629_add_hinh_thuc_thi_to_c_t_d_s_dang_kies_table', 1),
(44, '2025_04_17_084935_add_id_ds_dang_ky_to_bien_ban_hops_table', 1),
(45, '2025_04_26_173937_remove_id_vien_chuc_from_ctdsdangky', 1),
(46, '2025_04_26_173942_create_d_s_g_v_bien_soans_table', 1),
(47, '2025_04_26_191428_remove_so_gio_from_c_t_d_s_dang_kies_table', 1),
(48, '2025_04_26_191435_add_so_gio_to_d_s_g_v_bien_soans_table', 1),
(49, '2025_05_01_000002_change_id_hoc_phan_to_string', 1),
(50, '2025_05_01_030356_add_muc_do_to_cau_hois_table', 1),
(51, '2025_05_01_055648_add_timestamps_to_chuongs_table', 1),
(53, '2025_05_07_010636_add_trang_thai_to_bien_ban_hops_table', 2),
(54, '2025_05_10_161310_add_timestamps_and_question_counts_to_ma_trans_table', 3),
(55, '2025_05_10_172105_add_so_cau_muc_do_to_ma_trans_table', 4),
(56, '2025_05_18_144405_change_model_id_to_string_in_model_has_permissions', 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(3, 'App\\Models\\User', 'NV002');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 'admin'),
(2, 'App\\Models\\User', 'GV003'),
(3, 'App\\Models\\User', 'NV001'),
(3, 'App\\Models\\User', 'NV002'),
(4, 'App\\Models\\User', 'GV002'),
(5, 'App\\Models\\User', 'GV001');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhiem_vus`
--

CREATE TABLE `nhiem_vus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhiem_vus`
--

INSERT INTO `nhiem_vus` (`id`, `ten`, `able`, `created_at`, `updated_at`) VALUES
(1, 'Chủ tịch', 1, NULL, NULL),
(2, 'Thư ký', 1, NULL, NULL),
(3, 'Cán bộ phản biện', 1, NULL, NULL),
(4, 'Ủy viên', 1, '2025-05-06 17:50:29', '2025-05-06 17:50:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Tạo ngân hàng câu hỏi/đề thi', 'web', '2025-05-06 17:18:06', '2025-05-06 17:18:06'),
(2, 'Xuất đề thi', 'web', '2025-05-06 17:18:06', '2025-05-06 17:18:06'),
(3, 'Duyệt danh sách đăng ký', 'web', '2025-05-06 17:18:06', '2025-05-06 17:18:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2025-05-06 17:18:06', '2025-05-06 17:18:06'),
(2, 'Giảng viên', 'web', '2025-05-06 17:18:06', '2025-05-06 17:18:06'),
(3, 'Nhân viên P.ĐBCL', 'web', '2025-05-06 17:18:06', '2025-05-06 17:18:06'),
(4, 'Trưởng Bộ Môn', 'web', '2025-05-06 17:18:07', '2025-05-06 17:18:07'),
(5, 'Trưởng Khoa', 'web', '2025-05-06 17:18:07', '2025-05-06 17:18:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_baos`
--

CREATE TABLE `thong_baos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `files` varchar(255) DEFAULT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thong_baos`
--

INSERT INTO `thong_baos` (`id`, `title`, `content`, `files`, `able`, `created_at`, `updated_at`) VALUES
(13, 'dđ', 'dddđ', '[\"E:\\\\APP\\\\XAMPP\\\\htdocs\\\\exam_bank\\\\storage\\\\app\\/public\\/thongbao\\/6821b9070361a_appppppppppppppppppp.docx\"]', 1, '2025-05-12 09:01:59', '2025-05-12 09:01:59'),
(14, 'sssss', 'ssssssss', '[\"E:\\\\APP\\\\XAMPP\\\\htdocs\\\\exam_bank\\\\storage\\\\app\\/public\\/thongbao\\/6821b9af159c4_CT\\u0110T_CNTT-ban-h\\u00e0nh-theo-Q\\u0110-1225_16.11.2021-K63-64 (1).pdf\"]', 1, '2025-05-12 09:04:47', '2025-05-12 09:04:47'),
(15, 'sssss', 'sssssssss', '[\"E:\\\\APP\\\\XAMPP\\\\htdocs\\\\exam_bank\\\\storage\\\\app\\/public\\/thongbao\\/6821bbbb3d639_appppppppppppppppppp.docx\"]', 1, '2025-05-12 09:13:31', '2025-05-12 09:13:31'),
(16, 'ddđ', 'dddd', '[\"E:\\\\APP\\\\XAMPP\\\\htdocs\\\\exam_bank\\\\storage\\\\app\\/public\\/thongbao\\/6821bc53719a1_appppppppppppppppppp.docx\"]', 1, '2025-05-12 09:16:03', '2025-05-12 09:16:03'),
(17, 'ddđ', 'dddd', '[\"E:\\\\APP\\\\XAMPP\\\\htdocs\\\\exam_bank\\\\storage\\\\app\\/public\\/thongbao\\/6821bc76e94a8_appppppppppppppppppp.docx\"]', 1, '2025-05-12 09:16:38', '2025-05-12 09:16:38'),
(18, 's', 's', '[\"E:\\\\APP\\\\XAMPP\\\\htdocs\\\\exam_bank\\\\storage\\\\app\\/public\\/thongbao\\/6821bf2ecc0d2_CT\\u0110T_CNTT-ban-h\\u00e0nh-theo-Q\\u0110-1225_16.11.2021-K63-64 (1).pdf\"]', 1, '2025-05-12 09:28:14', '2025-05-12 09:28:14'),
(19, 's', 's', '[\"E:\\\\APP\\\\XAMPP\\\\htdocs\\\\exam_bank\\\\storage\\\\app\\/public\\/thongbao\\/6821bf8b6e67e_CT\\u0110T_CNTT-ban-h\\u00e0nh-theo-Q\\u0110-1225_16.11.2021-K63-64 (1).pdf\"]', 1, '2025-05-12 09:29:47', '2025-05-12 09:29:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` varchar(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `sdt` varchar(10) NOT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `ngay_sinh` date NOT NULL,
  `gioi_tinh` tinyint(1) NOT NULL,
  `id_bo_mon` varchar(6) NOT NULL,
  `id_chuc_vu` varchar(6) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `sdt`, `dia_chi`, `ngay_sinh`, `gioi_tinh`, `id_bo_mon`, `id_chuc_vu`, `remember_token`, `created_at`, `updated_at`, `able`) VALUES
('admin', 'Admin', 'tuyet.htn.63cntt@ntu.edu.vn', NULL, '$2y$12$qm7iST/H0uM3HobmAUFJ6.Ikstyr9iEpq7TOvWk/sWzU2.jOcXt3u', '0905123456', 'Thành phố Cần Thơ-Huyện Vĩnh Thạnh-Xã Vĩnh Trinh-', '2000-01-01', 0, 'admin', 'admin', '5qbvlf99q4wtc2STz8bEsMVgRXcFvAi5sqaOEz9RP4fXotn14r6X6fSWPEGF', '2025-05-06 17:18:10', '2025-05-06 17:18:10', 1),
('GV001', 'Nguyễn Văn A', 'huynhtuyet0201032019@gmail.com', NULL, '$2y$12$SiIpIgLwVNuuwWo57SPtV..2BAnZMDnnb8jnUioWRgEk9xLxMKCaq', '0905123456', 'Thành phố Cần Thơ-Huyện Vĩnh Thạnh-Xã Vĩnh Trinh-', '2000-01-01', 0, 'CNPM', 'tk', NULL, '2025-05-06 17:18:10', '2025-05-06 17:18:10', 1),
('GV002', 'Nguyễn Văn B', 'nguyenvanb@gmail.com', NULL, '$2y$12$QMwBhT02YOR0iX8FyeiEWOdbcdWgQHKtIU7L/QWM8u.cZf175Ute.', '0905123456', 'Thành phố Cần Thơ-Huyện Vĩnh Thạnh-Xã Vĩnh Trinh-', '2000-01-01', 0, 'CNPM', 'tbm', NULL, '2025-05-06 17:18:10', '2025-05-06 17:18:10', 1),
('GV003', 'Nguyen Văn D', 'nguyenvand@gmail.com', NULL, '$2y$12$YcHCI30RWUJwdyx9PptEZOIYrF/TE/3cfabum1z2oibVuGpp1VamS', '0937467868', 'Thành phố Cần Thơ-Huyện Vĩnh Thạnh-Xã Vĩnh Trinh-', '2025-05-06', 1, 'CNPM', 'gv', NULL, '2025-05-06 17:36:28', '2025-05-06 17:36:28', 1),
('NV001', 'Nguyễn Văn C', 'nguyenvanc@gmail.com', NULL, '$2y$12$1LrxKGHlytxkdghWx2ZRvukP3J.P0qVfyFauPV/4ohTNjRc2eFZWe', '0905123456', 'Thành phố Cần Thơ-Huyện Vĩnh Thạnh-Xã Vĩnh Trinh-', '2000-01-01', 0, 'dbcl', 'nvdbcl', NULL, '2025-05-06 17:18:10', '2025-05-18 08:00:35', 1),
('NV002', 'Nguyễn Văn E', 'nguyenvane@gmail.com', NULL, '$2y$12$wLuGfU.9HwAnhYcrxvcyceS6BN5SjuDvXUxbpUVCmOjobpqNp1Uma', '0937467866', 'Tỉnh An Giang-Huyện An Phú-Thị trấn An Phú-', '2025-05-18', 0, 'dbcl', 'nvdbcl', NULL, '2025-05-18 07:42:19', '2025-05-18 08:00:23', 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bac_dao_taos`
--
ALTER TABLE `bac_dao_taos`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `bien_ban_hops`
--
ALTER TABLE `bien_ban_hops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bien_ban_hops_id_ct_ds_dang_ky_foreign` (`id_ct_ds_dang_ky`),
  ADD KEY `bien_ban_hops_id_ds_dang_ky_foreign` (`id_ds_dang_ky`);

--
-- Chỉ mục cho bảng `bo_mons`
--
ALTER TABLE `bo_mons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bo_mons_id_khoa_foreign` (`id_khoa`);

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `cau_hois`
--
ALTER TABLE `cau_hois`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cau_hois_id_ct_ds_dang_ky_foreign` (`id_ct_ds_dang_ky`),
  ADD KEY `cau_hois_id_chuan_dau_ra_foreign` (`id_chuan_dau_ra`),
  ADD KEY `cau_hois_id_chuong_foreign` (`id_chuong`);

--
-- Chỉ mục cho bảng `chuan_dau_ras`
--
ALTER TABLE `chuan_dau_ras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chuan_dau_ras_id_hoc_phan_foreign` (`id_hoc_phan`);

--
-- Chỉ mục cho bảng `chuc_vus`
--
ALTER TABLE `chuc_vus`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `chuongs`
--
ALTER TABLE `chuongs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chuongs_id_hoc_phan_foreign` (`id_hoc_phan`);

--
-- Chỉ mục cho bảng `chuong_chuan_dau_ras`
--
ALTER TABLE `chuong_chuan_dau_ras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chuong_chuan_dau_ras_id_chuong_foreign` (`id_chuong`),
  ADD KEY `chuong_chuan_dau_ras_id_chuan_dau_ra_foreign` (`id_chuan_dau_ra`);

--
-- Chỉ mục cho bảng `c_t_de_this`
--
ALTER TABLE `c_t_de_this`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_t_de_this_id_de_thi_foreign` (`id_de_thi`),
  ADD KEY `c_t_de_this_id_cau_hoi_foreign` (`id_cau_hoi`);

--
-- Chỉ mục cho bảng `c_t_d_s_dang_kies`
--
ALTER TABLE `c_t_d_s_dang_kies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_t_d_s_dang_kies_id_ds_dang_ky_foreign` (`id_ds_dang_ky`),
  ADD KEY `c_t_d_s_dang_kies_id_hoc_phan_foreign` (`id_hoc_phan`);

--
-- Chỉ mục cho bảng `dap_ans`
--
ALTER TABLE `dap_ans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dap_ans_id_cau_hoi_foreign` (`id_cau_hoi`);

--
-- Chỉ mục cho bảng `de_this`
--
ALTER TABLE `de_this`
  ADD PRIMARY KEY (`id`),
  ADD KEY `de_this_id_hoc_phan_foreign` (`id_hoc_phan`),
  ADD KEY `de_this_id_lop_hoc_phan_foreign` (`id_lop_hoc_phan`);

--
-- Chỉ mục cho bảng `d_s_dang_kies`
--
ALTER TABLE `d_s_dang_kies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `d_s_dang_kies_id_bo_mon_foreign` (`id_bo_mon`);

--
-- Chỉ mục cho bảng `d_s_g_v_bien_soans`
--
ALTER TABLE `d_s_g_v_bien_soans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `d_s_g_v_bien_soans_id_ct_ds_dang_ky_foreign` (`id_ct_ds_dang_ky`),
  ADD KEY `d_s_g_v_bien_soans_id_vien_chuc_foreign` (`id_vien_chuc`);

--
-- Chỉ mục cho bảng `d_s_hops`
--
ALTER TABLE `d_s_hops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `d_s_hops_id_bien_ban_hop_foreign` (`id_bien_ban_hop`),
  ADD KEY `d_s_hops_id_nhiem_vu_foreign` (`id_nhiem_vu`),
  ADD KEY `d_s_hops_id_vien_chuc_foreign` (`id_vien_chuc`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `gio_quy_dois`
--
ALTER TABLE `gio_quy_dois`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `hoc_phans`
--
ALTER TABLE `hoc_phans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hoc_phans_id_bo_mon_foreign` (`id_bo_mon`),
  ADD KEY `hoc_phans_id_bac_dao_tao_foreign` (`id_bac_dao_tao`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `khoas`
--
ALTER TABLE `khoas`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `lop_hoc_phans`
--
ALTER TABLE `lop_hoc_phans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lop_hoc_phans_id_khoa_foreign` (`id_khoa`),
  ADD KEY `lop_hoc_phans_id_hoc_phan_foreign` (`id_hoc_phan`),
  ADD KEY `lop_hoc_phans_id_vien_chuc_foreign` (`id_vien_chuc`);

--
-- Chỉ mục cho bảng `ma_trans`
--
ALTER TABLE `ma_trans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ma_trans_id_chuan_dau_ra_foreign` (`id_chuan_dau_ra`),
  ADD KEY `ma_trans_id_chuong_foreign` (`id_chuong`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Chỉ mục cho bảng `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Chỉ mục cho bảng `nhiem_vus`
--
ALTER TABLE `nhiem_vus`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Chỉ mục cho bảng `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `thong_baos`
--
ALTER TABLE `thong_baos`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_id_bo_mon_foreign` (`id_bo_mon`),
  ADD KEY `users_id_chuc_vu_foreign` (`id_chuc_vu`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bien_ban_hops`
--
ALTER TABLE `bien_ban_hops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `cau_hois`
--
ALTER TABLE `cau_hois`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT cho bảng `chuan_dau_ras`
--
ALTER TABLE `chuan_dau_ras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `chuongs`
--
ALTER TABLE `chuongs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `chuong_chuan_dau_ras`
--
ALTER TABLE `chuong_chuan_dau_ras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `c_t_de_this`
--
ALTER TABLE `c_t_de_this`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `c_t_d_s_dang_kies`
--
ALTER TABLE `c_t_d_s_dang_kies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `dap_ans`
--
ALTER TABLE `dap_ans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT cho bảng `de_this`
--
ALTER TABLE `de_this`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `d_s_dang_kies`
--
ALTER TABLE `d_s_dang_kies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `d_s_g_v_bien_soans`
--
ALTER TABLE `d_s_g_v_bien_soans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `d_s_hops`
--
ALTER TABLE `d_s_hops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `gio_quy_dois`
--
ALTER TABLE `gio_quy_dois`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lop_hoc_phans`
--
ALTER TABLE `lop_hoc_phans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `ma_trans`
--
ALTER TABLE `ma_trans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT cho bảng `nhiem_vus`
--
ALTER TABLE `nhiem_vus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `thong_baos`
--
ALTER TABLE `thong_baos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bien_ban_hops`
--
ALTER TABLE `bien_ban_hops`
  ADD CONSTRAINT `bien_ban_hops_id_ct_ds_dang_ky_foreign` FOREIGN KEY (`id_ct_ds_dang_ky`) REFERENCES `c_t_d_s_dang_kies` (`id`),
  ADD CONSTRAINT `bien_ban_hops_id_ds_dang_ky_foreign` FOREIGN KEY (`id_ds_dang_ky`) REFERENCES `d_s_dang_kies` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `bo_mons`
--
ALTER TABLE `bo_mons`
  ADD CONSTRAINT `bo_mons_id_khoa_foreign` FOREIGN KEY (`id_khoa`) REFERENCES `khoas` (`id`);

--
-- Các ràng buộc cho bảng `cau_hois`
--
ALTER TABLE `cau_hois`
  ADD CONSTRAINT `cau_hois_id_chuan_dau_ra_foreign` FOREIGN KEY (`id_chuan_dau_ra`) REFERENCES `chuan_dau_ras` (`id`),
  ADD CONSTRAINT `cau_hois_id_chuong_foreign` FOREIGN KEY (`id_chuong`) REFERENCES `chuongs` (`id`),
  ADD CONSTRAINT `cau_hois_id_ct_ds_dang_ky_foreign` FOREIGN KEY (`id_ct_ds_dang_ky`) REFERENCES `c_t_d_s_dang_kies` (`id`);

--
-- Các ràng buộc cho bảng `chuan_dau_ras`
--
ALTER TABLE `chuan_dau_ras`
  ADD CONSTRAINT `chuan_dau_ras_id_hoc_phan_foreign` FOREIGN KEY (`id_hoc_phan`) REFERENCES `hoc_phans` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `chuongs`
--
ALTER TABLE `chuongs`
  ADD CONSTRAINT `chuongs_id_hoc_phan_foreign` FOREIGN KEY (`id_hoc_phan`) REFERENCES `hoc_phans` (`id`);

--
-- Các ràng buộc cho bảng `chuong_chuan_dau_ras`
--
ALTER TABLE `chuong_chuan_dau_ras`
  ADD CONSTRAINT `chuong_chuan_dau_ras_id_chuan_dau_ra_foreign` FOREIGN KEY (`id_chuan_dau_ra`) REFERENCES `chuan_dau_ras` (`id`),
  ADD CONSTRAINT `chuong_chuan_dau_ras_id_chuong_foreign` FOREIGN KEY (`id_chuong`) REFERENCES `chuongs` (`id`);

--
-- Các ràng buộc cho bảng `c_t_de_this`
--
ALTER TABLE `c_t_de_this`
  ADD CONSTRAINT `c_t_de_this_id_cau_hoi_foreign` FOREIGN KEY (`id_cau_hoi`) REFERENCES `cau_hois` (`id`),
  ADD CONSTRAINT `c_t_de_this_id_de_thi_foreign` FOREIGN KEY (`id_de_thi`) REFERENCES `de_this` (`id`);

--
-- Các ràng buộc cho bảng `c_t_d_s_dang_kies`
--
ALTER TABLE `c_t_d_s_dang_kies`
  ADD CONSTRAINT `c_t_d_s_dang_kies_id_ds_dang_ky_foreign` FOREIGN KEY (`id_ds_dang_ky`) REFERENCES `d_s_dang_kies` (`id`),
  ADD CONSTRAINT `c_t_d_s_dang_kies_id_hoc_phan_foreign` FOREIGN KEY (`id_hoc_phan`) REFERENCES `hoc_phans` (`id`);

--
-- Các ràng buộc cho bảng `dap_ans`
--
ALTER TABLE `dap_ans`
  ADD CONSTRAINT `dap_ans_id_cau_hoi_foreign` FOREIGN KEY (`id_cau_hoi`) REFERENCES `cau_hois` (`id`);

--
-- Các ràng buộc cho bảng `de_this`
--
ALTER TABLE `de_this`
  ADD CONSTRAINT `de_this_id_hoc_phan_foreign` FOREIGN KEY (`id_hoc_phan`) REFERENCES `hoc_phans` (`id`),
  ADD CONSTRAINT `de_this_id_lop_hoc_phan_foreign` FOREIGN KEY (`id_lop_hoc_phan`) REFERENCES `lop_hoc_phans` (`id`);

--
-- Các ràng buộc cho bảng `d_s_dang_kies`
--
ALTER TABLE `d_s_dang_kies`
  ADD CONSTRAINT `d_s_dang_kies_id_bo_mon_foreign` FOREIGN KEY (`id_bo_mon`) REFERENCES `bo_mons` (`id`);

--
-- Các ràng buộc cho bảng `d_s_g_v_bien_soans`
--
ALTER TABLE `d_s_g_v_bien_soans`
  ADD CONSTRAINT `d_s_g_v_bien_soans_id_ct_ds_dang_ky_foreign` FOREIGN KEY (`id_ct_ds_dang_ky`) REFERENCES `c_t_d_s_dang_kies` (`id`),
  ADD CONSTRAINT `d_s_g_v_bien_soans_id_vien_chuc_foreign` FOREIGN KEY (`id_vien_chuc`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `d_s_hops`
--
ALTER TABLE `d_s_hops`
  ADD CONSTRAINT `d_s_hops_id_bien_ban_hop_foreign` FOREIGN KEY (`id_bien_ban_hop`) REFERENCES `bien_ban_hops` (`id`),
  ADD CONSTRAINT `d_s_hops_id_nhiem_vu_foreign` FOREIGN KEY (`id_nhiem_vu`) REFERENCES `nhiem_vus` (`id`),
  ADD CONSTRAINT `d_s_hops_id_vien_chuc_foreign` FOREIGN KEY (`id_vien_chuc`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `hoc_phans`
--
ALTER TABLE `hoc_phans`
  ADD CONSTRAINT `hoc_phans_id_bac_dao_tao_foreign` FOREIGN KEY (`id_bac_dao_tao`) REFERENCES `bac_dao_taos` (`id`),
  ADD CONSTRAINT `hoc_phans_id_bo_mon_foreign` FOREIGN KEY (`id_bo_mon`) REFERENCES `bo_mons` (`id`);

--
-- Các ràng buộc cho bảng `lop_hoc_phans`
--
ALTER TABLE `lop_hoc_phans`
  ADD CONSTRAINT `lop_hoc_phans_id_hoc_phan_foreign` FOREIGN KEY (`id_hoc_phan`) REFERENCES `hoc_phans` (`id`),
  ADD CONSTRAINT `lop_hoc_phans_id_khoa_foreign` FOREIGN KEY (`id_khoa`) REFERENCES `khoas` (`id`),
  ADD CONSTRAINT `lop_hoc_phans_id_vien_chuc_foreign` FOREIGN KEY (`id_vien_chuc`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `ma_trans`
--
ALTER TABLE `ma_trans`
  ADD CONSTRAINT `ma_trans_id_chuan_dau_ra_foreign` FOREIGN KEY (`id_chuan_dau_ra`) REFERENCES `chuan_dau_ras` (`id`),
  ADD CONSTRAINT `ma_trans_id_chuong_foreign` FOREIGN KEY (`id_chuong`) REFERENCES `chuongs` (`id`);

--
-- Các ràng buộc cho bảng `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_id_bo_mon_foreign` FOREIGN KEY (`id_bo_mon`) REFERENCES `bo_mons` (`id`),
  ADD CONSTRAINT `users_id_chuc_vu_foreign` FOREIGN KEY (`id_chuc_vu`) REFERENCES `chuc_vus` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
