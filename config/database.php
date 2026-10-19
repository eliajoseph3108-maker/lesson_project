<?php 

$host ="localhost";
$dbname = "adminlte_demo";
$username = "root";
$password = "";

define('BASE_URL', '/lesson/');

try { 
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL,
        course VARCHAR(100) NOT NULL,
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    // Migrate table from earlier version (had no status column)
    if (!$pdo->query("SHOW COLUMNS FROM students LIKE 'status'")->fetch()) {
        $pdo->exec("ALTER TABLE students ADD status ENUM('Active','Inactive') DEFAULT 'Active'");
    }
    // New table: courses (added for this individual AdminLTE assignment)
    $pdo->exec("CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_name VARCHAR(120) NOT NULL UNIQUE,
        department VARCHAR(100) NOT NULL,
        duration VARCHAR(50) NOT NULL DEFAULT '3 Years',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    if ($pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO courses (course_name, department, duration) VALUES
            ('Computer Engineering', 'Engineering', '4 Years'),
            ('Information Technology', 'Computing', '3 Years'),
            ('Computer Science', 'Computing', '3 Years'),
            ('Business Information Systems', 'Business', '3 Years')");
    }

    // Seed sample data only when empty
    if ($pdo->query("SELECT COUNT(*) FROM students")->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO students (name, email, course, status) VALUES
            ('John Doe', 'john@example.com', 'Computer Engineering', 'Active'),
            ('Mary Jane', 'mary@example.com', 'Information Technology', 'Active'),
            ('Peter John', 'peter@example.com', 'Computer Science', 'Inactive')");
    }
}
catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}