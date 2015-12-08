<?php
    $migration = array();
    $migration['id']  =  "shader_2";
    $migration['query'] = "ALTER TABLE `".$DB_NAME."`.`shader` ADD `name` VARCHAR(255) NOT NULL AFTER `userid`;";
