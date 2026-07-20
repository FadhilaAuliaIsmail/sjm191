-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: sjm191_db
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `barang_produksi`
--

LOCK TABLES `barang_produksi` WRITE;
/*!40000 ALTER TABLE `barang_produksi` DISABLE KEYS */;
INSERT INTO `barang_produksi` (`id`, `sumber`, `nama_barang`, `user_id`, `tanggal_masuk`, `jumlah`, `satuan`, `jumlah_dibeli`, `berat_per_satuan`, `harga_beli`, `keterangan`, `created_at`, `updated_at`) VALUES (6,'sponsor','Susu Foyu',6,'2026-07-04',1000,'box',50,20.00,560000.00,'Pembelian dari foyu','2026-07-04 02:32:52','2026-07-06 03:50:07'),(7,'pasar','Jahe',6,'2026-07-04',500,'kg',1,500.00,500000.00,'Pasar Kramat Jati','2026-07-04 02:34:07','2026-07-04 02:34:24'),(8,'pasar','Gula merah',6,'2026-07-05',200,'kg',1,200.00,500000.00,NULL,'2026-07-05 06:41:41','2026-07-05 06:41:41');
/*!40000 ALTER TABLE `barang_produksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `data_mitra`
--

LOCK TABLES `data_mitra` WRITE;
/*!40000 ALTER TABLE `data_mitra` DISABLE KEYS */;
INSERT INTO `data_mitra` (`id`, `user_id`, `cabang`, `alamat`, `no_telp`, `created_at`, `updated_at`) VALUES (3,6,'Dapur SJM 191 DKI Jakarta','QXQ5+24H Kode Pos 13950, Jl Palad Rw. Kuning (Folding Gate Hijau, RT.6/RW.7, Pulo Gebang, Kec. Cakung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13950','08159088813','2026-07-05 18:26:50','2026-07-05 18:26:50');
/*!40000 ALTER TABLE `data_mitra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `detail_transaksi`
--

LOCK TABLES `detail_transaksi` WRITE;
/*!40000 ALTER TABLE `detail_transaksi` DISABLE KEYS */;
INSERT INTO `detail_transaksi` (`id`, `transaksi_id`, `produk_id`, `jumlah`, `harga`, `subtotal`, `created_at`, `updated_at`) VALUES (7,7,4,1,24000.00,24000.00,'2026-07-05 07:07:25','2026-07-05 07:07:25'),(8,7,5,1,14000.00,14000.00,'2026-07-05 07:07:25','2026-07-05 07:07:25'),(9,7,6,1,45000.00,45000.00,'2026-07-05 07:07:25','2026-07-05 07:07:25'),(10,7,7,1,17000.00,17000.00,'2026-07-05 07:07:25','2026-07-05 07:07:25'),(11,7,8,1,81000.00,81000.00,'2026-07-05 07:07:25','2026-07-05 07:07:25'),(12,7,9,1,13000.00,13000.00,'2026-07-05 07:07:25','2026-07-05 07:07:25'),(13,7,10,1,7000.00,7000.00,'2026-07-05 07:07:25','2026-07-05 07:07:25'),(14,8,13,2,24000.00,48000.00,'2026-07-05 18:11:06','2026-07-05 18:11:06'),(15,8,5,2,14000.00,28000.00,'2026-07-05 18:11:06','2026-07-05 18:11:06'),(16,9,4,1,24000.00,24000.00,'2026-07-06 04:08:25','2026-07-06 04:08:25'),(17,9,5,1,14000.00,14000.00,'2026-07-06 04:08:25','2026-07-06 04:08:25'),(18,9,6,1,45000.00,45000.00,'2026-07-06 04:08:25','2026-07-06 04:08:25'),(19,9,11,1,10000.00,10000.00,'2026-07-06 04:08:25','2026-07-06 04:08:25');
/*!40000 ALTER TABLE `detail_transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `pengguna`
--

LOCK TABLES `pengguna` WRITE;
/*!40000 ALTER TABLE `pengguna` DISABLE KEYS */;
INSERT INTO `pengguna` (`id`, `name`, `email`, `peran`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES (6,'Tumiran Azhari, S.pd','pemilik@gmail.com','pemilik_usaha',NULL,'$2y$12$FrcsiFe8kcGid5ckr1psO.BHapykWL0nhNgPCk/KTe77fDahiHxca',NULL,'2026-06-30 16:29:06','2026-07-02 21:29:53'),(7,'Surya Adi Nugraha S.Gz','kasir@gmail.com','kasir',NULL,'$2y$12$vArlOeN26TEYUIMBhN0j4OCDLCua.w1c2M1JRKOuRR1pmbpBTh.gC',NULL,'2026-06-30 16:29:52','2026-07-02 21:30:08');
/*!40000 ALTER TABLE `pengguna` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `produk`
--

LOCK TABLES `produk` WRITE;
/*!40000 ALTER TABLE `produk` DISABLE KEYS */;
INSERT INTO `produk` (`id`, `user_id`, `nama_produk`, `harga`, `stok`, `satuan`, `deskripsi`, `foto`, `created_at`, `updated_at`) VALUES (4,6,'Susu Foyu Jumbo',24000.00,997,'1000 gram',NULL,'produk/uvnbcxltKBMXcq8PHQFaINTofFln3fgyPlxdfOng.png','2026-07-04 02:06:15','2026-07-06 04:08:25'),(5,6,'Susu Foyu',14000.00,996,'500 gram',NULL,'produk/4RWAEWkmkVwWHKC65uLtH2keFmx4j1Z6HP79hpkK.png','2026-07-04 02:13:12','2026-07-06 04:08:25'),(6,7,'Madu',45000.00,98,'1 kg',NULL,'produk/LYwgZSADN0aYGRxgHcEBIZLoLwuuGOHBJuTQj5EV.png','2026-07-05 05:25:44','2026-07-06 04:08:25'),(7,7,'Gula Aren',17000.00,99,'250 gram',NULL,'produk/esFDzG4gG2EPLzt2eH7mhoEOopDNXP0dSEk3Pha2.png','2026-07-05 05:26:06','2026-07-05 07:07:25'),(8,7,'Bumbu Racikan',81000.00,49,'34 ons','1. Cabe jawa\r\n2. Kapulaga\r\n3. Kayu Manis\r\n4. Kayu Sintok\r\n5. Cengkeh\r\n6. Teh, dll','produk/1OtOq9WlHtOW4v9rBuxIR6fLCNlYjIpoJYc3Hw3s.png','2026-07-05 05:35:34','2026-07-05 07:07:25'),(9,7,'Plastik kiloan',13000.00,49,'250 gram',NULL,'produk/RNN7bdCWzcZlCxorMkxbGrujhnSwV6akaGzUor92.png','2026-07-05 05:38:09','2026-07-05 07:07:25'),(10,7,'Kayu Secang',7000.00,49,'100 gram',NULL,'produk/7FsW9omx2NmERwELB0r3U0FJESe6GuhNbCyuslFr.png','2026-07-05 05:40:10','2026-07-05 07:07:25'),(11,6,'Susu Jahe Merah Botol',10000.00,49,'250 ml',NULL,'produk/WYdHis5dpkrmcDsOK4aKHAJFOAv3UX7h3qTnuwNK.png','2026-07-05 10:55:35','2026-07-06 04:08:25'),(12,6,'Susu Jahe Merah Botol',12000.00,40,'1 liter',NULL,'produk/LCJQul7iveKJSmxMkiim7Bo8hMLJVMB4Se6WaopM.png','2026-07-05 10:56:57','2026-07-05 19:09:05'),(13,6,'Susu Jahe Merah Jerigen',24000.00,10,'2 liter',NULL,'produk/2QwUQsE887e5QrBtYl8mleXYcLmCL14BgsVt3xSQ.png','2026-07-05 10:59:18','2026-07-06 08:12:28');
/*!40000 ALTER TABLE `produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `transaksi`
--

LOCK TABLES `transaksi` WRITE;
/*!40000 ALTER TABLE `transaksi` DISABLE KEYS */;
INSERT INTO `transaksi` (`id`, `user_id`, `tanggal_transaksi`, `total_harga`, `bayar`, `kembalian`, `created_at`, `updated_at`) VALUES (4,7,'2026-06-30 23:57:18',12000.00,50000.00,38000.00,'2026-06-30 16:57:18','2026-06-30 16:57:18'),(5,7,'2026-07-01 00:02:03',12000.00,50000.00,38000.00,'2026-06-30 17:02:03','2026-06-30 17:02:03'),(6,7,'2026-07-03 08:15:58',45000.00,100000.00,55000.00,'2026-07-03 01:15:58','2026-07-03 01:15:58'),(7,7,'2026-07-05 14:07:25',201000.00,400000.00,199000.00,'2026-07-05 07:07:25','2026-07-05 07:07:25'),(8,7,'2026-07-06 01:11:06',76000.00,76000.00,0.00,'2026-07-05 18:11:06','2026-07-05 18:11:06'),(9,7,'2026-07-06 11:08:25',93000.00,100000.00,7000.00,'2026-07-06 04:08:25','2026-07-06 04:08:25');
/*!40000 ALTER TABLE `transaksi` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-20 15:51:27
