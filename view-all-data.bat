@echo off
echo ========================================
echo    PICTORTS DATABASE VIEWER
echo ========================================
echo.
echo This will show you ALL your data:
echo - Users who have accounts
echo - Documents you've created
echo - Financial records you've added
echo - Todo items you've created
echo.
echo Press any key to continue...
pause
echo.

echo ========================================
echo 1. VIEWING ALL USERS
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot pictorts -e "SELECT CONCAT('ID: ', id) as 'User ID', CONCAT('Name: ', name) as Name, CONCAT('Email: ', email) as Email, CONCAT('Role: ', role) as Role, CONCAT('Office ID: ', COALESCE(office_id, 'None')) as 'Office' FROM users ORDER BY id;"
echo.
echo ========================================
echo 2. VIEWING RECENT DOCUMENTS
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot pictorts -e "SELECT CONCAT('Doc ID: ', id) as 'Document', CONCAT('DTS #: ', dts_number) as 'DTS Number', CONCAT('PICTO #: ', picto_number) as 'PICTO Number', CONCAT('Type: ', document_type) as Type, CONCAT('Subject: ', LEFT(subject, 50)) as Subject, CONCAT('Status: ', status) as Status, CONCAT('Created: ', DATE_FORMAT(created_at, '%Y-%m-%d %H:%i')) as 'Created Date' FROM documents ORDER BY created_at DESC LIMIT 15;"
echo.
echo ========================================
echo 3. VIEWING FINANCIAL RECORDS
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot pictorts -e "SELECT CONCAT('ID: ', id) as 'Financial ID', CONCAT('Type: ', type) as Type, CONCAT('Description: ', LEFT(description, 60)) as Description, CONCAT('PR #: ', COALESCE(pr_number, 'None')) as 'PR Number', CONCAT('Amount: ', COALESCE(pr_amount, 0)) as 'PR Amount', CONCAT('Status: ', status) as Status, CONCAT('Created: ', DATE_FORMAT(created_at, '%Y-%m-%d %H:%i')) as 'Created Date' FROM financial_records ORDER BY created_at DESC LIMIT 15;"
echo.
echo ========================================
echo 4. VIEWING TODO ITEMS
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot pictorts -e "SELECT CONCAT('ID: ', id) as 'Todo ID', CONCAT('Title: ', LEFT(title, 50)) as Title, CONCAT('Status: ', status) as Status, CONCAT('Priority: ', priority) as Priority, CONCAT('Assigned To: ', COALESCE(assigned_to, 'Unassigned')) as 'Assigned To', CONCAT('Due: ', COALESCE(DATE_FORMAT(due_date, '%Y-%m-%d'), 'No due date')) as 'Due Date', CONCAT('Created: ', DATE_FORMAT(created_at, '%Y-%m-%d %H:%i')) as 'Created Date' FROM todos ORDER BY created_at DESC LIMIT 15;"
echo.
echo ========================================
echo 5. VIEWING DOCUMENT FILES (ATTACHMENTS)
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot pictorts -e "SELECT CONCAT('File ID: ', id) as 'File ID', CONCAT('Document ID: ', document_id) as 'Document ID', CONCAT('Filename: ', filename) as 'Filename', CONCAT('File Size: ', file_size) as 'Size (bytes)', CONCAT('Uploaded: ', DATE_FORMAT(created_at, '%Y-%m-%d %H:%i')) as 'Upload Date' FROM document_files ORDER BY created_at DESC LIMIT 10;"
echo.
echo ========================================
echo 6. VIEWING FINANCIAL ATTACHMENTS
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot pictorts -e "SELECT CONCAT('Attachment ID: ', id) as 'Attachment ID', CONCAT('Financial ID: ', financial_id) as 'Financial ID', CONCAT('Filename: ', filename) as 'Filename', CONCAT('File Size: ', file_size) as 'Size (bytes)', CONCAT('Uploaded: ', DATE_FORMAT(created_at, '%Y-%m-%d %H:%i')) as 'Upload Date' FROM financial_attachments ORDER BY created_at DESC LIMIT 10;"
echo.
echo ========================================
echo SUMMARY COUNTS
echo ========================================
echo.
docker exec pictorts_db mysql -u root -proot pictorts -e "SELECT 'Total Users' as Metric, COUNT(*) as Count FROM users UNION ALL SELECT 'Total Documents', COUNT(*) FROM documents UNION ALL SELECT 'Total Financial Records', COUNT(*) FROM financial_records UNION ALL SELECT 'Total Todo Items', COUNT(*) FROM todos UNION ALL SELECT 'Total Document Files', COUNT(*) FROM document_files UNION ALL SELECT 'Total Financial Attachments', COUNT(*) FROM financial_attachments;"
echo.
echo ========================================
echo Done viewing your PICTORTS data!
echo ========================================
pause
