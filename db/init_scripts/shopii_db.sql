-- this is the script the .yaml file will use to initialise and setup the database
-- notes:
-- 1. passwords are in plaintext for ez reference; remember to hash them for hardening later

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00";

CREATE DATABASE IF NOT EXISTS `shopii` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `shopii`;

-- create products table
CREATE TABLE `products` (
	`product_id`	int NOT NULL AUTO_INCREMENT,
	`name`	varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
	`price`	decimal(4,2) NOT NULL,
	`picture`	varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
	`description`	varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
	PRIMARY KEY(`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- insert into products table
INSERT INTO `products` (`product_id`, `name`, `price`, `picture`, `description`) VALUES
(1, "Razor Headphones", 79.90, "headphones.jpg", "Surround Sound 8D Audio Gaming Razor Headphones"),
(2, "Marshall & Carter Soap Bar", 139.90, "soap.jpg", "High quality branded soap. Guaranteed League player repellent."),
(3, "Panther Sports Shoes", 90.50, "sportshoes.jpeg", "Comfortable, rugged running shoes from Panther."),
(4, "Delroy Wallet", 57.90, "wallet.jpg", "Bifold leather wallet made by the best."),
(5, "Patriot Yoga Mat", 20.00, "yogamat.jpg", "Bundle sale included. Limited time for National Day!");

-- -----

-- create users tables
CREATE TABLE `users` (
    `user_id` int NOT NULL AUTO_INCREMENT,
    `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `email` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `password` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `profilepic` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
    `privilege` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- insert into users table
INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `profilepic`, `privilege`) VALUES
(1, "gaydenchio", "gaydenchio@gmail.com", "$2a$10$4N.kHs/YG8FNRw1lPZQXqecnFSqdaFIRDwXNEMlr9IAIs7zWh0EDO", '', 'user'),
(2, 'takato_3244278', 'takato@cybermail.jp', '$2a$10$DMY8htvvNlc3LmNuag.Z2e7Nq8mxJFO1VyXQbePpk6qwPlwzXTQpG', '', 'user'),
(3, 'kasm79', 'kasm@proton.me', 'ZnFyYnRuaHdxeHduaA==', '', 'user'),
(4, 'admin1', 'admin@shopii.com', '$2a$10$rRUUL9SWReuH0Cq3pi97EeLDVweAxv4tU0yyc07EcmP.lJx0.4Uoa', 'admin');

-- -----

-- create comments table
CREATE TABLE `comments` (
    `comment_id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `comment` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `posted_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`comment_id`),
    FOREIGN KEY(`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- insert into comments table
INSERT INTO `comments` (`comment_id`, `user_id`, `comment`, `posted_on`) VALUES
(1, 3, 'Do you remember the 21st night of September?', '2022-09-21 20:18:36'),
(2, 1, 'YU XIAAAAAAAAAANGGGG', '2024-04-20 14:20:15'),
(3, 4, 'This kinda good ngl', '2023-05-11 15:20:56');

-- -----

-- create cart table
CREATE TABLE `cart` (
    `cart_id` int NOT NULL AUTO_INCREMENT,
    `cart_productid` int NOT NULL,
    `cart_userid` int NOT NULL,
    `qty` int NOT NULL,
    PRIMARY KEY(`cart_id`),
    FOREIGN KEY(`cart_productid`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
    FOREIGN KEY(`cart_userid`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- insert into cart table
INSERT INTO `cart` (`cart_id`, `cart_productid`, `cart_userid`, `qty`) VALUES
(50, 1, 2, 1),
(51, 2, 2, 50),
(52, 3, 3, 2),
(53, 2, 1, 1);

COMMIT;
