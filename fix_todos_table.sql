-- Fix the todos table to add AUTO_INCREMENT to the id field
-- First, make sure id is the primary key
ALTER TABLE `todos` ADD PRIMARY KEY (`id`);
-- Then modify the id field to be AUTO_INCREMENT
ALTER TABLE `todos` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
