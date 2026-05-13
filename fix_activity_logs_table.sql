-- Fix the activity_logs table to add AUTO_INCREMENT to the id field
-- First, make sure id is the primary key (it already is based on DESCRIBE output)
ALTER TABLE `activity_logs` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
