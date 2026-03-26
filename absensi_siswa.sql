-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2026 at 10:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_siswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `status` enum('hadir','sakit','izin','alpha') DEFAULT 'hadir',
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `siswa_id`, `tanggal`, `jam_masuk`, `status`, `keterangan`) VALUES
(1, 1, '2026-03-18', '00:00:00', 'izin', ''),
(2, 1, '2026-03-25', '00:00:00', 'sakit', ''),
(3, 1, '2026-03-24', '07:00:00', 'hadir', 'Tepat waktu'),
(4, 2, '2026-03-24', '07:15:00', 'hadir', ''),
(5, 3, '2026-03-24', '00:00:00', 'alpha', 'Tanpa keterangan'),
(6, 4, '2026-03-24', '00:00:00', 'sakit', 'Demam'),
(7, 5, '2026-03-24', '07:05:00', 'hadir', ''),
(8, 1, '2026-03-25', '07:10:00', 'hadir', ''),
(9, 2, '2026-03-25', '00:00:00', 'izin', 'Acara keluarga'),
(10, 3, '2026-03-25', '07:00:00', 'hadir', ''),
(11, 4, '2026-03-25', '07:02:00', 'hadir', ''),
(12, 5, '2026-03-25', '00:00:00', 'alpha', '');

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_guru` varchar(100) DEFAULT NULL,
  `mapel` varchar(100) DEFAULT NULL,
  `is_walikelas` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `user_id`, `nama_guru`, `mapel`, `is_walikelas`) VALUES
(3, 12, 'Gabriel', 'PWPB', '1');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `kelas_jurusan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `user_id`, `nis`, `nama_lengkap`, `kelas_jurusan`) VALUES
(1, 3, '11111', ' Budi', 'XI RPL'),
(2, 4, '11112', 'Ani Wijaya', 'XI RPL'),
(3, 5, '11113', 'Citra Lestari', 'XI TKJ'),
(4, 6, '11114', 'Dedi Kurniawan', 'XI TKJ'),
(5, 7, '11115', 'Eka Saputra', 'XI RPL'),
(6, 15, '12345', 'Asep', 'X DKV');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','guru','siswa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(3, '11111', '$2y$10$zV1T6vT9eGyeA.e79S0z6O53OVa1ifNiwrlYiuosTQUUthDWScDQa', 'siswa'),
(4, '11112', '$2y$10$zV1T6vT9eGyeA.e79S0z6O53OVa1ifNiwrlYiuosTQUUthDWScDQa', 'siswa'),
(5, '11113', '$2y$10$zV1T6vT9eGyeA.e79S0z6O53OVa1ifNiwrlYiuosTQUUthDWScDQa', 'siswa'),
(6, '11114', '$2y$10$zV1T6vT9eGyeA.e79S0z6O53OVa1ifNiwrlYiuosTQUUthDWScDQa', 'siswa'),
(7, '11115', '$2y$10$zV1T6vT9eGyeA.e79S0z6O53OVa1ifNiwrlYiuosTQUUthDWScDQa', 'siswa'),
(9, '00000', '$2y$10$ZbHG0l5GaKbAxI2HIRln6.mReHajRqZSZZucGuxwPij3zWFky39Mi', 'admin'),
(12, '0990990', '$2y$10$xjqpa7mhp0Po6FExGjoPOeY/Z28DhWOklLcK6cvkAkErKkH3UN58y', 'guru'),
(15, '12345', '$2y$10$BRNcpZNclS6kMkdEA411fuWPVc4E8J6bhIZi8yjAJtFBSj0UAbqQa', 'siswa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD KEY `siswa_ibfk_2` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`);

--
-- Constraints for table `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
