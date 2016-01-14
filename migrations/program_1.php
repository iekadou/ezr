<?php
$migration = array();
$migration['id']  =  "program_1";
$migration['query'] = "CREATE TABLE `".$DB_NAME."`.`program` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `userid` int(11) NOT NULL, `name` varchar(255) NOT NULL, `init_id` int(11) NOT NULL, `render_id` int(11) NOT NULL);";
