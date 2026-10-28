-- Run once against your existing uber_cueillette database after a backup.
-- Widens storage only: existing plain-text values are NOT converted.
ALTER TABLE agriculteur MODIFY COLUMN password VARCHAR(255) NOT NULL;
ALTER TABLE ouvrier MODIFY COLUMN password VARCHAR(255) NOT NULL;
