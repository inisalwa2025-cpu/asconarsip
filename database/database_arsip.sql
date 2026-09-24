-- Import this file after selecting the target database in phpMyAdmin.
-- Local database: database_arsip
-- InfinityFree database: if0_42923619_database_arsip


CREATE TABLE IF NOT EXISTS documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  document_number VARCHAR(100),
  category VARCHAR(50) NOT NULL,
  file_type VARCHAR(20),
  file_size VARCHAR(50),
  document_date DATE,
  source VARCHAR(255),
  file_name VARCHAR(255),
  file_data LONGBLOB,
  is_deleted TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

SET @source_exists = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'source'
);
SET @source_sql = IF(
  @source_exists = 0,
  'ALTER TABLE documents ADD COLUMN source VARCHAR(255) NULL AFTER document_date',
  'SELECT 1'
);
PREPARE add_source FROM @source_sql;
EXECUTE add_source;
DEALLOCATE PREPARE add_source;

SET @file_data_exists = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'file_data'
);
SET @file_data_sql = IF(
  @file_data_exists = 0,
  'ALTER TABLE documents ADD COLUMN file_data LONGBLOB NULL AFTER file_name',
  'SELECT 1'
);
PREPARE add_file_data FROM @file_data_sql;
EXECUTE add_file_data;
DEALLOCATE PREPARE add_file_data;
