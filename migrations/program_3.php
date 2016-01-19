<?php
$migration = array();
$migration['id']  =  "program_3";
$migration['query'] = "ALTER TABLE `".$DB_NAME."`.`program` ADD `object_type` INT NOT NULL DEFAULT '0' ;";
