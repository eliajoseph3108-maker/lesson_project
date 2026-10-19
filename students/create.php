<?php
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../components.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO students (name, email, course, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([trim($_POST['name']), trim($_POST['email']), trim($_POST['course']), $_POST['status']]);
    header("Location: read.php");
    exit;
}
require __DIR__ . "/../include/header.php";
?>
<h3 class="mb-3">Add Student</h3>
<div class="card"><div class="card-body"><?php student_form([], "Save"); ?></div></div>
<?php require __DIR__ . "/../include/footer.php"; ?>
