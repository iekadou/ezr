<?php
$migration = array();
$migration['id']  =  "renderpass_1";
$migration['query'] = "CREATE TABLE `".$DB_NAME."`.`renderpass` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `program_id` int(11) NOT NULL, `shader_id` int(11) NOT NULL, `rank` int(11) NOT NULL, `enabled` tinyint(1) NOT NULL, `needs_swap` tinyint(1) NOT NULL, `userid` INT NOT NULL, `texture_name` VARCHAR(255) NOT NULL);";
