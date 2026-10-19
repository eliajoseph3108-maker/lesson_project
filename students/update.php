<?php
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../components.php";

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$student) { header("Location: read.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE students SET name=?, email=?, course=?, status=? WHERE id=?");
    $stmt->execute([trim($_POST['name']), trim($_POST['email']), trim($_POST['course']), $_POST['status'], $id]);
    header("Location: read.php");
    exit;
}
require __DIR__ . "/../include/header.php";
?>
<h3 class="mb-3">Edit Student</h3>
<div class="card"><div class="card-body"><?php student_form($student, "Update"); ?></div></div>
<?php require __DIR__ . "/../include/footer.php"; ?>
