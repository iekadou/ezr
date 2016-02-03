<?php
$migration = array();
$migration['id']  =  "program_4";
$migration['query'] = "INSERT INTO `".$DB_NAME."`.`renderpass` (`id`, `program_id`, `shader_id`, `rank`, `enabled`, `needs_swap`, `userid`, `texture_name`) select NULL, join_table.program_id, join_table.shader_id, '0', '0', '0', join_table.userid, 'tDiffuse' from (select p1.id as program_id, p1.userid as userid, s1.id as shader_id from shader as s1, program as p1 where p1.material_id = s1.id) as join_table";
