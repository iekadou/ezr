<?php
$migration = array();
$migration['id']  =  "shaderpass_1";
$migration['query'] = "CREATE TABLE `".$DB_NAME."`.`shaderpass` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `program_id` int(11) NOT NULL, `shader_id` int(11) NOT NULL, `rank` int(11) NOT NULL, `enabled` tinyint(1) NOT NULL, `needs_swap` tinyint(1) NOT NULL);";
