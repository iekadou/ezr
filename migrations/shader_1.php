<?php
$migration = array();
$migration['id']  =  "shader_1";
$migration['query'] = "CREATE TABLE `".$DB_NAME."`.`shader` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `userid` int(11) NOT NULL, `name` varchar(255) NOT NULL, `vertex_id` int(11) NOT NULL, `fragment_id` int(11) NOT NULL);";
