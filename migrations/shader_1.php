<?php
    $migration = array();
    $migration['id']  =  "shader_1";
    $migration['query'] = "CREATE TABLE `".$DB_NAME."`.`shader` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `userid` int(11) NOT NULL, `type` int(11) NOT NULL, `program` text NOT NULL);";
