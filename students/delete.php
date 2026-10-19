<?php
require __DIR__ . "/../config/database.php";

$id = (int)($_GET['id'] ?? 0);
$pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
header("Location: read.php");
exit;
