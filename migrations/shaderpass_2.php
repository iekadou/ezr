<?php
$migration = array();
$migration['id']  =  "shaderpass_2";
$migration['query'] = "ALTER TABLE `".$DB_NAME."`.`shaderpass` ADD `userid` INT NOT NULL ;";
