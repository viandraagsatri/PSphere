-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 05, 2026 at 02:26 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_rental_ps`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_pelanggan` (IN `p_id` INT)   BEGIN
    DELETE FROM pelanggan
    WHERE id_pelanggan = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_insert_pelanggan` (IN `p_nama` VARCHAR(100), IN `p_nohp` VARCHAR(20))   BEGIN
    INSERT INTO pelanggan(nama_pelanggan, no_hp)
    VALUES(p_nama, p_nohp);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_select_pelanggan` ()   BEGIN
    SELECT * FROM pelanggan;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_pelanggan` (IN `p_id` INT, IN `p_nama` VARCHAR(100), IN `p_nohp` VARCHAR(20))   BEGIN
    UPDATE pelanggan
    SET nama_pelanggan = p_nama,
        no_hp = p_nohp
    WHERE id_pelanggan = p_id;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_total_bayar` (`p_harga` DECIMAL(10,2), `p_jam` INT) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    RETURN p_harga * p_jam;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id_booking` int NOT NULL,
  `id_pelanggan` int NOT NULL,
  `id_ps` int NOT NULL,
  `jam_mulai` datetime NOT NULL,
  `jam_selesai` datetime NOT NULL,
  `total_jam` int NOT NULL,
  `status_booking` enum('booking','aktif','selesai','batal') NOT NULL DEFAULT 'booking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id_booking`, `id_pelanggan`, `id_ps`, `jam_mulai`, `jam_selesai`, `total_jam`, `status_booking`) VALUES
(1, 1, 1, '2025-07-04 08:00:00', '2025-07-04 13:00:00', 5, 'selesai'),
(2, 5, 4, '2026-06-04 19:10:00', '2026-06-04 21:10:00', 2, 'selesai'),
(3, 1, 1, '2026-06-04 19:11:00', '2026-06-04 21:11:00', 2, 'selesai'),
(5, 4, 3, '2026-06-04 22:00:00', '2026-06-04 23:00:00', 1, 'selesai'),
(6, 5, 3, '2026-06-04 23:00:00', '2026-06-05 00:00:00', 1, 'selesai'),
(7, 2, 1, '2026-06-04 23:50:00', '2026-06-05 00:50:00', 1, 'selesai');

--
-- Triggers `booking`
--
DELIMITER $$
CREATE TRIGGER `trg_booking_ps_dipakai` AFTER INSERT ON `booking` FOR EACH ROW BEGIN
    UPDATE ps_unit
    SET status_ps = 'dipakai'
    WHERE id_ps = NEW.id_ps;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_booking_selesai` AFTER UPDATE ON `booking` FOR EACH ROW BEGIN
    IF NEW.status_booking = 'selesai' THEN
        UPDATE ps_unit
        SET status_ps = 'tersedia'
        WHERE id_ps = NEW.id_ps;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_hp`, `alamat`) VALUES
(1, 'Via', '08123456789', 'Bandar Lampung'),
(2, 'Arif', '08987654321', 'Metro'),
(3, 'Chay', '08202033443', 'Lampung Tengah'),
(4, 'Muthi', '08123455889', ''),
(5, 'yara', '089998776889', '');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `kategori` enum('makanan','minuman','camilan') NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `kategori`, `harga`, `stok`) VALUES
(1, 'Indomie Goreng + Telur', 'makanan', '12000.00', 50),
(2, 'Es Teh Manis', 'minuman', '4000.00', 100),
(3, 'Keripik Kentang', 'camilan', '8000.00', 30),
(4, 'Kopi Susu', 'minuman', '10000.00', 40);

-- --------------------------------------------------------

--
-- Table structure for table `ps_unit`
--

CREATE TABLE `ps_unit` (
  `id_ps` int NOT NULL,
  `nama_ps` varchar(50) NOT NULL,
  `tipe` enum('PS3','PS4','PS5') NOT NULL,
  `harga_per_jam` decimal(10,2) NOT NULL,
  `status_ps` enum('tersedia','dipakai','pemeliharaan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ps_unit`
--

INSERT INTO `ps_unit` (`id_ps`, `nama_ps`, `tipe`, `harga_per_jam`, `status_ps`) VALUES
(1, 'PS 01', 'PS4', '10000.00', 'tersedia'),
(2, 'PS 02', 'PS5', '15000.00', 'pemeliharaan'),
(3, 'PS 03', 'PS3', '8000.00', 'tersedia'),
(4, 'PS 04', 'PS4', '10000.00', 'tersedia'),
(5, 'PS 05', 'PS5', '15000.00', 'tersedia'),
(6, 'PS 06', 'PS4', '10000.00', 'tersedia'),
(7, 'PS 07', 'PS5', '15000.00', 'tersedia'),
(8, 'PS 08', 'PS3', '8000.00', 'tersedia'),
(9, 'PS 09', 'PS4', '10000.00', 'tersedia'),
(10, 'PS 10', 'PS5', '15000.00', 'tersedia'),
(11, 'PS 11', 'PS4', '10000.00', 'tersedia'),
(12, 'PS 12', 'PS5', '15000.00', 'tersedia'),
(13, 'PS 13', 'PS3', '8000.00', 'tersedia'),
(14, 'PS 14', 'PS4', '10000.00', 'tersedia'),
(15, 'PS 15', 'PS5', '15000.00', 'tersedia'),
(16, 'PS 16', 'PS4', '10000.00', 'tersedia'),
(17, 'PS 17', 'PS5', '15000.00', 'tersedia'),
(18, 'PS 18', 'PS3', '8000.00', 'tersedia'),
(19, 'PS 19', 'PS4', '10000.00', 'tersedia'),
(20, 'PS 20', 'PS5', '15000.00', 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id_log` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `waktu` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_booking` int NOT NULL,
  `id_user` int DEFAULT '2',
  `total_bayar` decimal(10,2) NOT NULL,
  `biaya_tambahan` decimal(10,2) DEFAULT '0.00',
  `diskon` decimal(10,2) DEFAULT '0.00',
  `metode_bayar` enum('cash','qris','transfer') NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `status_pembayaran` enum('lunas','dp','belum_bayar') NOT NULL DEFAULT 'belum_bayar',
  `jumlah_dibayar` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_booking`, `id_user`, `total_bayar`, `biaya_tambahan`, `diskon`, `metode_bayar`, `tanggal_transaksi`, `status_pembayaran`, `jumlah_dibayar`) VALUES
(3, 1, 2, '50000.00', '0.00', '0.00', 'cash', '2026-06-04 14:50:54', 'lunas', '50000.00'),
(7, 2, 2, '20000.00', '0.00', '0.00', 'cash', '2026-06-04 22:12:31', 'lunas', '0.00'),
(8, 3, 2, '20000.00', '0.00', '0.00', 'transfer', '2026-06-04 22:13:37', 'lunas', '0.00'),
(9, 5, 2, '8000.00', '0.00', '0.00', 'qris', '2026-06-04 23:05:46', 'lunas', '0.00'),
(10, 6, 2, '8000.00', '0.00', '0.00', 'qris', '2026-06-04 23:09:40', 'lunas', '0.00'),
(13, 1, 1, '30000.00', '0.00', '0.00', 'cash', '2026-06-04 23:43:50', 'lunas', '0.00'),
(15, 1, 1, '30000.00', '0.00', '0.00', 'cash', '2026-06-04 23:52:42', 'lunas', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_produk`
--

CREATE TABLE `transaksi_produk` (
  `id_trx_produk` int NOT NULL,
  `id_booking` int NOT NULL,
  `id_produk` int NOT NULL,
  `qty` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `status_pesanan` enum('pending','diantar','selesai') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `transaksi_produk`
--
DELIMITER $$
CREATE TRIGGER `kurangi_stok_produk` AFTER INSERT ON `transaksi_produk` FOR EACH ROW BEGIN
    UPDATE produk 
    SET stok = stok - NEW.qty 
    WHERE id_produk = NEW.id_produk;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_user`, `username`, `password`, `role`) VALUES
(1, 'Administrator', 'admin', 'admin123', 'admin'),
(2, 'Kasir', 'kasir', 'kasir123', 'kasir'),
(3, 'Kasir 1', 'kasir1', '123456', 'kasir'),
(4, 'Kasir 2', 'kasir2', '123456', 'kasir');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_laporan_aktivitas`
-- (See below for the actual view)
--
CREATE TABLE `v_laporan_aktivitas` (
`id_data` int
,`nama_pelanggan` varchar(100)
,`nama_ps` varchar(50)
,`tanggal` datetime
,`aktivitas` varchar(9)
,`status` varchar(11)
);

-- --------------------------------------------------------

--
-- Structure for view `v_laporan_aktivitas`
--
DROP TABLE IF EXISTS `v_laporan_aktivitas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_laporan_aktivitas`  AS SELECT `b`.`id_booking` AS `id_data`, `p`.`nama_pelanggan` AS `nama_pelanggan`, `ps`.`nama_ps` AS `nama_ps`, `b`.`jam_mulai` AS `tanggal`, 'BOOKING' AS `aktivitas`, `b`.`status_booking` AS `status` FROM ((`booking` `b` join `pelanggan` `p` on((`b`.`id_pelanggan` = `p`.`id_pelanggan`))) join `ps_unit` `ps` on((`b`.`id_ps` = `ps`.`id_ps`))) union all select `t`.`id_transaksi` AS `id_data`,`p`.`nama_pelanggan` AS `nama_pelanggan`,`ps`.`nama_ps` AS `nama_ps`,`t`.`tanggal_transaksi` AS `tanggal`,'TRANSAKSI' AS `aktivitas`,`t`.`status_pembayaran` AS `status` from (((`transaksi` `t` join `booking` `b` on((`t`.`id_booking` = `b`.`id_booking`))) join `pelanggan` `p` on((`b`.`id_pelanggan` = `p`.`id_pelanggan`))) join `ps_unit` `ps` on((`b`.`id_ps` = `ps`.`id_ps`)))  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `fk_booking_pelanggan` (`id_pelanggan`),
  ADD KEY `fk_booking_ps` (`id_ps`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `ps_unit`
--
ALTER TABLE `ps_unit`
  ADD PRIMARY KEY (`id_ps`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `fk_logs_user` (`id_user`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_booking` (`id_booking`),
  ADD KEY `fk_transaksi_user` (`id_user`);

--
-- Indexes for table `transaksi_produk`
--
ALTER TABLE `transaksi_produk`
  ADD PRIMARY KEY (`id_trx_produk`),
  ADD KEY `fk_detail_produk` (`id_produk`),
  ADD KEY `fk_detail_booking` (`id_booking`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ps_unit`
--
ALTER TABLE `ps_unit`
  MODIFY `id_ps` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id_log` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `transaksi_produk`
--
ALTER TABLE `transaksi_produk`
  MODIFY `id_trx_produk` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_ps` FOREIGN KEY (`id_ps`) REFERENCES `ps_unit` (`id_ps`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_booking` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_produk`
--
ALTER TABLE `transaksi_produk`
  ADD CONSTRAINT `fk_detail_booking` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE RESTRICT ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `reset_status_ps` ON SCHEDULE EVERY 1 DAY STARTS '2026-06-05 18:29:43' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Mengubah status PS menjadi tersedia hanya jika jam_selesai-nya sudah lewat
    UPDATE ps_unit 
    SET status_ps = 'tersedia'
    WHERE id_ps IN (
        SELECT id_ps FROM booking 
        WHERE jam_selesai <= NOW() AND status_booking = 'aktif'
    );
    
    -- Sekaligus update status booking yang kelupaan di-close oleh kasir
    UPDATE booking 
    SET status_booking = 'selesai'
    WHERE jam_selesai <= NOW() AND status_booking = 'aktif';
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
