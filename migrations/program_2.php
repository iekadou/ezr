<?php
$migration = array();
$migration['id']  =  "program_2";
$migration['query'] = "ALTER TABLE `".$DB_NAME."`.`program` ADD `material_id` INT NOT NULL ;";
