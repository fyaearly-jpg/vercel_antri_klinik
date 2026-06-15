-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Apr 2026 pada 18.29
-- Versi server: 10.4.32-MariaDB-log
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `klinik_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `antrian`
--

CREATE TABLE `antrian` (
  `id` int(11) NOT NULL,
  `nomor_antrean` varchar(10) NOT NULL,
  `status` enum('menunggu','dipanggil','selesai') DEFAULT 'menunggu',
  `id_pasien` int(11) DEFAULT NULL,
  `poli` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `antrian`
--

INSERT INTO `antrian` (`id`, `nomor_antrean`, `status`, `id_pasien`, `poli`, `created_at`, `updated_at`) VALUES
(1, '0', 'selesai', 1, 'Gigi', '2026-04-09 01:54:21', '2026-04-09 03:51:08'),
(2, '0', 'selesai', 2, 'Umum', '2026-04-09 03:50:28', '2026-04-09 05:51:03'),
(3, '0', 'selesai', 2, 'Umum', '2026-04-09 03:51:25', '2026-04-09 06:24:39'),
(4, '0', 'selesai', 2, 'Umum', '2026-04-09 06:23:51', '2026-04-09 07:05:32'),
(5, '0', 'selesai', 2, 'Umum', '2026-04-09 06:24:52', '2026-04-09 10:03:52'),
(6, '0', 'dipanggil', 2, 'Umum', '2026-04-09 07:04:59', '2026-04-09 10:03:52'),
(7, 'B-7', 'menunggu', 2, 'Gigi', '2026-04-09 10:22:24', '2026-04-09 10:22:24'),
(8, 'B-8', 'menunggu', 2, 'Gigi', '2026-04-09 10:25:27', '2026-04-09 10:25:27'),
(9, 'B-9', 'menunggu', 2, 'Gigi', '2026-04-09 10:25:42', '2026-04-09 10:25:42'),
(10, 'B-10', 'menunggu', 2, 'Gigi', '2026-04-09 10:27:27', '2026-04-09 10:27:27'),
(11, 'A-1', 'menunggu', 2, 'Umum', '2026-04-13 04:22:53', '2026-04-13 04:22:53'),
(12, 'A-2', 'menunggu', 2, 'Umum', '2026-04-13 04:47:53', '2026-04-13 04:47:53'),
(13, 'G-3', 'menunggu', 3, 'Gigi', '2026-04-13 07:37:14', '2026-04-13 07:37:14'),
(14, 'U-4', 'menunggu', 4, 'Umum', '2026-04-13 13:43:21', '2026-04-13 13:43:21'),
(15, 'U-5', 'menunggu', 4, 'Umum', '2026-04-13 13:58:44', '2026-04-13 13:58:44'),
(16, 'U-6', 'menunggu', 4, 'Umum', '2026-04-13 14:14:54', '2026-04-13 14:14:54'),
(17, 'U-7', 'menunggu', 4, 'Umum', '2026-04-13 14:25:27', '2026-04-13 14:25:27'),
(18, 'U-8', 'menunggu', 4, 'Umum', '2026-04-13 14:32:29', '2026-04-13 14:32:29'),
(19, 'A-9', 'menunggu', 6, 'Anak', '2026-04-13 14:39:55', '2026-04-13 14:39:55'),
(20, 'U-10', 'menunggu', 4, 'Umum', '2026-04-13 14:43:56', '2026-04-13 14:43:56'),
(21, 'G-11', 'menunggu', 6, 'Gigi', '2026-04-13 14:52:15', '2026-04-13 14:52:15'),
(22, 'G-12', 'menunggu', 4, 'Gigi', '2026-04-13 14:54:52', '2026-04-13 14:54:52'),
(23, 'A-13', 'menunggu', 6, 'Anak', '2026-04-13 15:02:02', '2026-04-13 15:02:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `nama_pasien` varchar(100) DEFAULT NULL,
  `kepuasan` varchar(50) DEFAULT NULL,
  `saran` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `feedback`
--

INSERT INTO `feedback` (`id`, `nama_pasien`, `kepuasan`, `saran`, `created_at`) VALUES
(1, 'Fathiya Early', 'Cukup', 'HSHSHAS', '2026-04-13 07:43:46'),
(2, 'alisha', 'Puas', '', '2026-04-13 15:01:27'),
(3, 'levi', 'Puas', '', '2026-04-13 15:02:47'),
(4, 'levi', 'Puas', '', '2026-04-13 15:02:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pasien`
--

CREATE TABLE `pasien` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nama_pasien` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'pasien',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pasien`
--

INSERT INTO `pasien` (`id`, `email`, `nama_pasien`, `password`, `role`, `created_at`) VALUES
(1, '3519035703060001', 'fya', '$2y$10$T3pspkhlblkQdC0wNIWtJu9A7QCcE4Uszx.U2k7bQ9usOaHKCJ/EG', 'pasien', '2026-04-08 11:30:54'),
(2, '3510123456789101', 'Megumi Zenin', '$2y$10$cK3TxzB99XGvztGWr2TiAeGZQ42SjCFBs4WQid0T1CstS0bA0k3.S', 'pasien', '2026-04-09 03:34:45'),
(3, 'margareth6371@gmail.com', 'Fathiya Early', '$2y$10$OBs4K3VtcZTVFJnAN4eE0.hwyf5X.QXJLt4nUlrcYMlXc7Z.JbaWy', 'pasien', '2026-04-13 07:25:12'),
(4, 'ambar@gmail.com', 'alisha', '$2y$10$2ifbtD879u4UXn37EjPlBOX.Hag2m4yaymG8mGdi0L352k9/J2uo.', 'pasien', '2026-04-13 09:05:51'),
(6, 'levikujiro@gmail.com', 'levi', '$2y$10$rkIE9o68/n39jYUuEH4xBOHFCd.CqaeuP.ErvIQXJrk.KzqbZBUeW', 'pasien', '2026-04-13 14:38:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas`
--

CREATE TABLE `petugas` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `petugas`
--

INSERT INTO `petugas` (`id`, `nama_lengkap`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Ika wahyu', 'anindyamonica@gmail.com', '$2y$10$U5Ve/UiEIAl8S6vdLqYUNusNXKF/bM90cWmH7G.Ff/1DmxAz9ErEi', 'admin', '2026-04-13 07:45:28'),
(7, 'fayza elaine', 'fayzaelaine@gmail.com', '$2y$10$poP0TLlYG.u8NmF28vfd2eBuYexZSOufsn0NV.IxJJmGPNR2SqPWa', 'staff', '2026-04-13 13:47:51');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `antrian`
--
ALTER TABLE `antrian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pasien` (`id_pasien`);

--
-- Indeks untuk tabel `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`email`);

--
-- Indeks untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `antrian`
--
ALTER TABLE `antrian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `antrian`
--
ALTER TABLE `antrian`
  ADD CONSTRAINT `antrian_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
