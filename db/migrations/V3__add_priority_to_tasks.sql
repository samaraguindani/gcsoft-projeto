ALTER TABLE tasks ADD COLUMN priority ENUM('low','medium','high') NOT NULL DEFAULT 'medium' AFTER description;
