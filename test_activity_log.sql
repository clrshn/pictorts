USE pictorts;
INSERT INTO activity_logs (user_id, action, title, description, properties, subject_id, subject_type, created_at, updated_at) VALUES (1, 'created', 'Task created', 'Test task created', '{"status":"pending","priority":"high"}', 999, 'App\\Models\\Todo', NOW(), NOW());
