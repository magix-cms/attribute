CREATE TABLE IF NOT EXISTS `mc_attribute` (
   `id_attr` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
   `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id_attr`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_attribute_content` (
    `id_content` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_attr` int(7) UNSIGNED NOT NULL,
    `id_lang` smallint(3) UNSIGNED NOT NULL,
    `type_attr` varchar(40) NULL,
    `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_content`),
    KEY `id_attr` (`id_attr`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_attribute_value` (
  `id_attr_va` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_attr` int(7) UNSIGNED NOT NULL,
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_attr_va`),
  KEY `id_attr` (`id_attr`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_attribute_value_content` (
    `id_content` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_attr_va` int(7) UNSIGNED NOT NULL,
    `id_lang` smallint(3) UNSIGNED NOT NULL,
    `value_attr` varchar(40) NULL,
    `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_content`),
    KEY `id_attr_va` (`id_attr_va`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_attribute_category` (
    `id_attr_ca` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_attr` int(7) UNSIGNED NOT NULL,
    `id_cat` int(7) UNSIGNED NOT NULL,
    `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_attr_ca`),
    KEY `id_attr` (`id_attr`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_attribute_product` (
    `id_attr_p` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_attr_va` int(7) UNSIGNED NOT NULL,
    `id_product` int(11) UNSIGNED NOT NULL,
    `price_p` decimal(12,2) DEFAULT NULL,
    `order_attr_p` smallint(5) UNSIGNED NOT NULL,
    `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_attr_p`),
    KEY `id_attr_va` (`id_attr_va`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_cartpay_attribute` (
    `id_cart_attr` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_attr_va` int(7) UNSIGNED NOT NULL,
    `id_items` int(7) UNSIGNED NOT NULL,
    `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_cart_attr`),
    KEY `id_attr_va` (`id_attr_va`),
    KEY `id_items` (`id_items`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `mc_attribute_content` ADD FOREIGN KEY (`id_attr`) REFERENCES `mc_attribute`(`id_attr`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `mc_attribute_value_content` ADD FOREIGN KEY (`id_attr_va`) REFERENCES `mc_attribute_value`(`id_attr_va`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `mc_attribute_category` ADD FOREIGN KEY (`id_attr`) REFERENCES `mc_attribute`(`id_attr`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `mc_attribute_product` ADD FOREIGN KEY (`id_attr_va`) REFERENCES `mc_attribute_value`(`id_attr_va`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `mc_cartpay_attribute` ADD FOREIGN KEY (`id_attr_va`) REFERENCES `mc_attribute_product`(`id_attr_va`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `mc_cartpay_attribute` ADD FOREIGN KEY (`id_items`) REFERENCES `mc_cartpay_items`(`id_items`) ON DELETE CASCADE ON UPDATE RESTRICT;