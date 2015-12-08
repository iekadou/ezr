<?php
    $migration = array();
    $migration['id']  =  "remember_1";
    $migration['query'] = "CREATE TABLE `".$DB_NAME."`.`remember` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `token` varchar(256) NOT NULL, `userid` int(11) NOT NULL, `expires` int(15) NOT NULL);";
