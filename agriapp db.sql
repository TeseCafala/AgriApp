CREATE DATABASE agriapp;
USE agriapp;

-- 1. Tabelas independentes (sem foreign keys)
CREATE TABLE `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` VARCHAR(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `consumer` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `delivery_address` VARCHAR(500) NOT NULL,
  `profile_image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `deliveries` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tracking_code` VARCHAR(50) NOT NULL,
  `delivery_date` DATE NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tracking_code` (`tracking_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `farmers_` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `bank_account` VARCHAR(50) NOT NULL,
  `profile_image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `reviews` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rating` INT(11) NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment` VARCHAR(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `admin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `profile_image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tabelas com FK para farmers_
CREATE TABLE `productsz` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `quantity_avaliable` INT(11) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `imagem` VARCHAR(255) DEFAULT NULL,
  `farmer_id` INT(11) NOT NULL,
  `ativo` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_farmer` (`farmer_id`),
  CONSTRAINT `fk_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `farmers_` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Tabela de pedidos (referencia farmers_ e productsz)
CREATE TABLE `pedidos_` (
  `order_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `farmer_id` INT(11) NOT NULL,
  `order_date` DATETIME NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `total_value` DECIMAL(10,2) NOT NULL CHECK (`total_value` > 0),
  `payment_method` ENUM('credit_card','debit_card','paypal','pix') NOT NULL,
  `status` ENUM('Pending','Completed','Shipped','Cancelled') NOT NULL DEFAULT 'Pending',
  `item_count` INT(11) NOT NULL CHECK (`item_count` > 0),
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`order_id`),
  KEY `idx_farmer_id` (`farmer_id`),
  CONSTRAINT `fk_pedidos_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `farmers_` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Tabela de itens de pedido (referencia pedidos_, productsz, farmers_)
CREATE TABLE `order_items` (
  `order_item_id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `price_at_order` DECIMAL(10,2) NOT NULL,
  `farmer_id` INT(11) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  KEY `fk_order_items_farmer_id` (`farmer_id`),
  CONSTRAINT `fk_order_items_farmer_id` FOREIGN KEY (`farmer_id`) REFERENCES `farmers_` (`id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `pedidos_` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `productsz` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Pagamentos (referencia pedidos_)
CREATE TABLE `payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `commission_rate` DECIMAL(5,2) NOT NULL,
  `payment_date` DATE NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `transfer_date` DATE DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `pedidos_` (`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Mensagens
CREATE TABLE `mensagens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `remetente_id` INT NOT NULL,
  `destinatario_id` INT NOT NULL,
  `tipo_remetente` ENUM('cliente', 'agricultor') NOT NULL,
  `mensagem` TEXT NOT NULL,
  `status` ENUM('lida', 'nao_lida') DEFAULT 'nao_lida',
  `data_envio` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Reset de senha
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. Inserção de admin inicial
INSERT INTO `admin` (`name`, `email`, `phone`, `password`)
VALUES ('Admin', 'adminagriapp@gmail.com', '949086521', 'admin1234');

ALTER TABLE pedidos_ MODIFY payment_method VARCHAR(50);

ALTER TABLE consumer ADD COLUMN archived TINYINT(1) DEFAULT 0;
ALTER TABLE farmers_ ADD COLUMN archived TINYINT(1) DEFAULT 0;


