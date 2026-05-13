USE pictorts;

-- Fix users table
ALTER TABLE `users` ADD PRIMARY KEY (`id`);
ALTER TABLE `users` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix documents table
ALTER TABLE `documents` ADD PRIMARY KEY (`id`);
ALTER TABLE `documents` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix approvals table
ALTER TABLE `approvals` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix pins table
ALTER TABLE `pins` ADD PRIMARY KEY (`id`);
ALTER TABLE `pins` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix document_files table
ALTER TABLE `document_files` ADD PRIMARY KEY (`id`);
ALTER TABLE `document_files` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix document_routes table
ALTER TABLE `document_routes` ADD PRIMARY KEY (`id`);
ALTER TABLE `document_routes` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix financial_attachments table
ALTER TABLE `financial_attachments` ADD PRIMARY KEY (`id`);
ALTER TABLE `financial_attachments` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix financial_routes table
ALTER TABLE `financial_routes` ADD PRIMARY KEY (`id`);
ALTER TABLE `financial_routes` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix notification_preferences table
ALTER TABLE `notification_preferences` ADD PRIMARY KEY (`id`);
ALTER TABLE `notification_preferences` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix saved_filters table
ALTER TABLE `saved_filters` ADD PRIMARY KEY (`id`);
ALTER TABLE `saved_filters` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
