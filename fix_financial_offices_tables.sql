USE pictorts;

-- Fix financial_records table
ALTER TABLE `financial_records` ADD PRIMARY KEY (`id`);
ALTER TABLE `financial_records` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- Fix offices table
ALTER TABLE `offices` ADD PRIMARY KEY (`id`);
ALTER TABLE `offices` MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
