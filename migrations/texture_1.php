<?php
$migration = array();
$migration['id']  =  "texture_1";
$migration['query'] = "CREATE TABLE `".$DB_NAME."`.`texture` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `program_id` int(11) NOT NULL, `userid` INT NOT NULL, `name` VARCHAR(255) NOT NULL, `img` VARCHAR(255) NOT NULL);";
