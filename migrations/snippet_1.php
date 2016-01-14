<?php
$migration = array();
$migration['id']  =  "snippet_1";
$migration['query'] = "CREATE TABLE `".$DB_NAME."`.`snippet` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `userid` int(11) NOT NULL, `name` varchar(255) NOT NULL, `type` int(11) NOT NULL, `code` text NOT NULL);";
