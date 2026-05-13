USE pictorts;

-- Fix comments table
ALTER TABLE `comments` ADD PRIMARY KEY (`id`);
ALTER TABLE `comments` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix todo_subtasks table
ALTER TABLE `todo_subtasks` ADD PRIMARY KEY (`id`);
ALTER TABLE `todo_subtasks` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
