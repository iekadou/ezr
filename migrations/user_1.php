<?php
    $migration = array();
    $migration['id']  =  "user_1";
    $migration['query'] = "CREATE TABLE `".$DB_NAME."`.`user` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `username` varchar(20) NOT NULL UNIQUE, `password` varchar(256) NOT NULL, `email` varchar(50) NOT NULL, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `activated` tinyint(1) NOT NULL, `activation_key` varchar(20) NOT NULL, `is_deleted` tinyint(1) NOT NULL DEFAULT '0', `deleted_at` int(14) NOT NULL);";
