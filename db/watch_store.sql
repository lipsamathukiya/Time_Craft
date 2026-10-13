-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2026 at 06:32 PM
-- Server version: 10.1.33-MariaDB
-- PHP Version: 7.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `watch_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `id` int(50) NOT NULL,
  `Brand Name` varchar(50) NOT NULL,
  `Brand Description` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id`, `Brand Name`, `Brand Description`) VALUES
(1, 'Rolex', 'Indian Watch'),
(2, 'Casio', 'Indian Watch'),
(3, 'Titan', 'Indian Brand'),
(4, 'Fossil', 'Indian Watchs');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `product_id`, `product_name`, `price`, `qty`, `created_at`, `user_id`) VALUES
(23, 13, 'Regalia', '30000.00', 1, '2026-08-24 03:43:07', 6);

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'lipsa', 'lipsa2@gmail.com', 'sdfghm,', 'asdfgbjh,.xcv', '2026-08-08 04:07:35');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(10) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_price` decimal(10,0) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,0) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `order_status` varchar(50) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `admin_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `customer_name`, `email`, `phone`, `address`, `product_name`, `product_price`, `quantity`, `total_price`, `payment_method`, `order_status`, `order_date`, `admin_deleted`) VALUES
(2, 'khushali', 'khushali@gmail.com', '3265365326', 'surat', 'Riley Shine', '450', 1, '450', 'Cash on Delivery', 'Delivered', '2026-08-26 02:49:42', 0),
(3, 'mansvi', 'khushali@gmail.com', '2356256235', 'rajkot', 'Regalia', '30000', 1, '30000', 'Cash on Delivery', 'Shipped', '2026-08-26 02:49:24', 0),
(4, 'demo', 'demo@gmail.com', '1234567890', 'demo', 'Sea-Dweller', '2000', 4, '8000', 'Cash On Delivery', 'Confirmed', '2026-08-26 03:55:41', 0),
(5, 'lipsa', 'lipsa2@gmail.com', '8975687964', 'jetpur', 'Edifice', '20500', 1, '20500', 'Cash On Delivery', 'Confirmed', '2026-09-16 12:20:05', 0),
(8, 'pinal', 'pinal@gmail.com', '5623656236', 'surat', 'Crystal Lady', '55000', 1, '55000', 'Cash On Delivery', 'Pending', '2026-09-14 05:52:18', 0),
(9, 'lipsa', 'lipsa2@gmail.com', '5623656236', 'rajkot', 'Rose Glow', '52000', 3, '156000', 'Cash On Delivery', 'Delivered', '2026-09-16 10:12:29', 1),
(10, 'lipsa', 'lipsa2@gmail.com', '5623656236', 'rajkot', 'Crystal Lady', '55000', 1, '55000', 'Cash On Delivery', 'Delivered', '2026-09-16 10:11:38', 1),
(16, 'lipsa', 'lipsa2@gmail.com', '6595695895', 'mumbai', 'Elegant Shine', '45000', 1, '45000', 'Cash On Delivery', 'Cancelled', '2026-09-16 12:04:09', 1),
(17, 'mansvi', 'mansvi@gmail.com', '6595695895', 'rajkot', 'Crystal Lady', '55000', 1, '55000', 'Cash On Delivery', 'Pending', '2026-09-15 16:42:16', 0),
(18, 'pinal', 'pinal@gmail.com', '6595695895', 'surat', 'Golden Charm', '60000', 1, '60000', 'Cash On Delivery', 'Delivered', '2026-09-16 10:27:29', 1),
(19, 'lipsa', 'lipsa2@gmail.com', '1233344444', 'mumbai', 'Rose Glow', '52000', 1, '52000', 'Cash On Delivery', 'Pending', '2026-09-16 16:39:48', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `collection` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `product_name`, `brand`, `price`, `image`, `collection`, `stock`) VALUES
(2, 'Sea-Dweller', 'Rolex', '2000.00', '1786250603m126603-0001.avif', 'Men', 200),
(3, ' Day-Date ', 'Rolex', '37900.00', '178625105970437490056781.jpg', 'Men', 35),
(4, 'Submariner', 'Rolex', '85000.00', '178625123115692298695821804.jpg', 'Men', 8),
(6, 'Daytona', 'Rolex', '12500.00', '178625156520125529581391616.jpg', 'Men', 60),
(7, 'G-Shock', 'Casio', '79991.00', '1786251637[casio] Analog Silver Metal Band White Men Mtp-1370d-7a2vdf Watch _ Tracking.jpg', 'Men', 39),
(8, 'Edifice', 'Casio', '20500.00', '1786251663140806234564001.jpg', 'Men', 7),
(9, 'Enticer', 'Casio', '30000.00', '17862516891900024838758604.jpg', 'men', 0),
(10, 'Pro Trek', 'Casio', '50000.00', '1786251737Casio Enticer Multicolour Dial Analog Watch for Men.jpg', 'Men', 8),
(11, 'Edge', 'Titan', '22000.00', '1786251772l2.jpg', 'Men', 9),
(12, 'Octane', 'Titan', '52000.00', '1786251815Titan Edge Brown Dial Ultra-thin Stainless Steel Menâ€™s Elite Luxury Analog Watch.jpg', 'men', 0),
(13, 'Regalia', 'Titan', '30000.00', '1786251843tita2.jpg', 'Men', 30),
(14, 'Neo', 'Titan', '19000.00', '1786251891Titan Minimalist Black Dial Stainless Steel Watch â€“ Premium Elegant Design.jpg', 'Men', 6),
(15, 'Grant', 'Fossil', '35000.00', '1786251925477663104246183504.jpg', 'men', 0),
(16, 'Townsman', 'Fossil', '40000.00', '1786251954611926668159485682.jpg', 'men', 0),
(17, 'Minimalist', 'Fossil', '66500.00', '1786251983fo1.jpg', 'Men', 19),
(18, 'Defender', 'Fossil', '35000.00', '1786252020fosi2.jpg', 'Men', 34),
(19, 'Lady Elegance', 'Rolex', '15000.00', '17862530471900024839914404.jpg', 'Women', 67),
(20, 'Pearl Grace', 'Rolex', '8500.00', '17862530941407443629344418.jpg', 'Women', 45),
(21, 'Rose Gold Lady', 'Rolex', '9200.00', '17862531302040762328699060.jpg', 'Women', 4),
(22, 'Diamond Charm', 'Rolex', '19500.00', '1786253215Rolex watch â¤ï¸ðŸ¥³.jpg', 'women', 0),
(23, 'Sheen Classic', 'Casio', '20000.00', '1786253249âœ¨ Quiet Luxury, Loud Flex â€” The Casio Watch That Goes With Everything âœ¨.jpg', 'Women', 43),
(24, 'Rose Glow', 'Casio', '52000.00', '178625329641306521577775304.jpg', 'Women', 46),
(25, 'Elegant Shine', 'Casio', '45000.00', '1786265838casuo1.jpg', 'Women', 30),
(26, 'Crystal Lady', 'Casio', '55000.00', '1786265902Watch love ,donâ€™t pause.jpg', 'Women', 74),
(27, 'Raga Grace', 'Titan', '60000.00', '1786265939Classic Elegant Watches for Women in Amazon.jpg', 'Men', 33),
(28, 'Raga Pearl', 'Titan', '35000.00', '1786265984Add a touch of glamour to your everyday look.jpg', 'women', 0),
(29, 'Rose Elegance', 'Titan', '34000.00', '1786266013Elegant Titan Rose Gold Watch âŒšâœ¨ _ Luxury Womenâ€™s Watch for Classy Style.jpg', 'women', 0),
(30, 'Golden Charm', 'Titan', '75000.00', '1786266077Titan Womens Watch Rose Gold Jewelry Bracelet Brown Dial 35mm Nr95140km01.jpg', 'Women', 44),
(31, 'Carlie Rose', 'Fossil', '16000.00', '17862663023025924741683532.jpg', 'Women', 55),
(32, 'Jacqueline Pearl', 'Fossil', '25000.00', '1786266354Fossil Accessories _ New In Box! Fossil Watch _ Color_ Gold_White _ Size_ Os.jpg', 'women', 0),
(33, 'Raquel Gold', 'Fossil', '35000.00', '1786266382fo2.jpg', 'Women', 6),
(34, 'Riley Shine', 'Fossil', '450.00', '1786266435fo4.jpg', 'Women', 60),
(35, 'Diamond Elite', 'Rolex', '12000.00', '1786268470p1.jfif', 'Men', 10),
(36, 'Golden Crown', 'Rolex', '2500.00', '1786289790p2.jfif', 'Luxury', 20),
(37, 'Diamond Luxe', 'Rolex', '30000.00', '1786289847p3.jfif', 'Luxury', 39),
(38, 'Crown Master', 'Rolex', '35000.00', '1786290004p4.jfif', 'Luxury', 79),
(40, 'OnePlus Watch 2', 'Titan', '35000.00', '1786291946p2.jfif', 'Smart', 90),
(41, 'Titan Edge', 'Titan', '5000.00', '1786292158p9.jfif', 'Luxury', 50);

-- --------------------------------------------------------

--
-- Table structure for table `reg`
--

CREATE TABLE `reg` (
  `id` int(200) NOT NULL,
  `name` varchar(300) NOT NULL,
  `email` varchar(300) NOT NULL,
  `pass` varchar(400) NOT NULL,
  `phone` int(200) NOT NULL,
  `address` varchar(100) NOT NULL,
  `gender` varchar(200) NOT NULL,
  `u_type` varchar(50) NOT NULL,
  `login_date` date NOT NULL,
  `login_time` varchar(50) DEFAULT NULL,
  `logout_date` date NOT NULL,
  `logout_time` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reg`
--

INSERT INTO `reg` (`id`, `name`, `email`, `pass`, `phone`, `address`, `gender`, `u_type`, `login_date`, `login_time`, `logout_date`, `logout_time`) VALUES
(1, 'khushali', 'khushali@gmail.com', '12345678', 2147483647, 'surat', 'Female', 'user', '2026-09-12', '07:12:55 AM', '2026-09-11', '09:44:33 AM'),
(2, 'lipsa', 'lipsa2@gmail.com', '12345678', 2147483647, 'rajkot', 'Female', 'admin', '2026-09-24', '05:34:30 PM', '2026-09-24', '08:35:59 PM'),
(3, 'demo', 'demo@gmail.com', 'demo1234', 1234567890, 'demo', 'Female', 'user', '2026-08-26', '06:04:46 AM', '2026-08-26', '09:35:03 AM'),
(4, 'pinal', 'pinal@gmail.com', 'pinal123', 2147483647, 'surat', 'Female', 'user', '2026-09-24', '05:35:05 PM', '2026-09-24', '09:47:13 PM'),
(5, 'mansvi', 'mansvi@gmail.com', 'mansvi12', 2147483647, 'surat', 'Female', 'user', '2026-09-15', '06:41:52 PM', '0000-00-00', '00:00:00'),
(6, 'yug', 'yug@gmail.com', 'yug12345', 2147483647, 'jetpur', 'Male', 'user', '2026-09-24', '09:56:18 PM', '2026-09-24', '09:57:11 PM');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`) VALUES
(1, 7, 2),
(2, 7, 3),
(3, 7, 4),
(4, 10, 3),
(5, 6, 9),
(6, 20, 3),
(7, 20, 4),
(8, 20, 6),
(9, 20, 21),
(10, 1, 8),
(11, 3, 2),
(12, 2, 8),
(13, 2, 30),
(14, 1, 9),
(15, 1, 41);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reg`
--
ALTER TABLE `reg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `reg`
--
ALTER TABLE `reg`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
