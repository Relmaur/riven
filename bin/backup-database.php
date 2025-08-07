<?php

require_once 'vendor/autoload.php';

use Core\Console;

$dbPath = 'database/database.sqlite';
$backupDir = 'database/backups/';

// Check if the database file exists
if (!file_exists($dbPath)) {
    Console::error("Database file not found at '{$dbPath}'");
    exit(1);
}

// Check if the backup directory exists, if not, create it.
if (!is_dir($backupDir)) {
    if (!mkdir($backupDir, 0755, true)) {
        Console::error("Failed to create backup directory at '{$backupDir}'");
        exit(1);
    }
}

// Create a unique, timestamped filename for the backup
$backupFile = $backupDir . 'backup_' . date('Y-m-d_His') . '.sqlite';


// Copy the database file to the backup location
if (copy($dbPath, $backupFile)) {
    Console::success("Database backup created successfully at '{$backupFile}'. Go break some stuff.");
} else {
    Console::error("Failed to create database backup");
    exit(1);
}
