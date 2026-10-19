<?php
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../include/header.php";
$students = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="d-flex justify-content-between mb-3">
    <h3>Students</h3>
    <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Student</a>
</div>
<div class="card"><div class="card-body p-0">
<table class="table table-striped mb-0">
    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Course</th><th>Status</th><th width="160">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($students as $s): ?>
        <tr>
            <td><?= $s['id'] ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= htmlspecialchars($s['course']) ?></td>
            <td><span class="badge text-bg-<?= $s['status'] === 'Active' ? 'success' : 'secondary' ?>"><?= $s['status'] ?></span></td>
            <td>
                <a href="update.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                <a href="delete.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this student?')"><i class="bi bi-trash"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$students): ?><tr><td colspan="6" class="text-center">No students yet.</td></tr><?php endif; ?>
    </tbody>
</table>
</div></div>
<?php require __DIR__ . "/../include/footer.php"; ?>
