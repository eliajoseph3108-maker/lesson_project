<?php
require __DIR__ . "/config/database.php";
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseName = trim($_POST['course_name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    if ($courseName === '' || $department === '' || $duration === '') {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (course_name, department, duration) VALUES (?, ?, ?)");
            $stmt->execute([$courseName, $department, $duration]);
            $message = 'Course added successfully.';
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000' ? 'That course already exists.' : 'Could not add the course. Please check your database.';
        }
    }
}
$courses = $pdo->query("SELECT c.*, COUNT(s.id) AS student_count FROM courses c LEFT JOIN students s ON s.course=c.course_name GROUP BY c.id ORDER BY c.id DESC")->fetchAll(PDO::FETCH_ASSOC);
require __DIR__ . "/include/header.php";
?>
<div class="d-flex justify-content-between align-items-center mb-3"><div><h3 class="mb-1">Courses</h3><p class="text-secondary mb-0">New database table: <code>courses</code></p></div><span class="badge text-bg-success p-2"><?= count($courses) ?> courses</span></div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-primary card-outline">
            <div class="card-header"><h5 class="card-title">Add New Course</h5></div>
            <form method="post">
                <div class="card-body">
                    <div class="mb-3"><label for="course_name" class="form-label">Course name</label><input class="form-control" id="course_name" name="course_name" maxlength="120" required></div>
                    <div class="mb-3"><label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department" name="department" required><option value="">Choose department...</option><option>Engineering</option><option>Computing</option><option>Business</option><option>Science</option><option>Other</option></select>
                    </div>
                    <div class="mb-3"><label for="duration" class="form-label">Duration</label>
                        <select class="form-select" id="duration" name="duration" required><option value="">Choose duration...</option><option>1 Year</option><option>2 Years</option><option>3 Years</option><option>4 Years</option><option>5 Years</option></select>
                    </div>
                </div>
                <div class="card-footer"><button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle me-1"></i>Add Course</button></div>
            </form>
        </div>
        <div class="callout callout-info"><h5>About this table</h5><p class="mb-0">The <code>courses</code> table stores the course name, department, duration and date created. The student count is calculated from the students table.</p></div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title">Courses Table</h5></div>
            <div class="table-responsive"><table class="table table-bordered table-striped table-hover mb-0">
                <thead><tr><th>ID</th><th>Course Name</th><th>Department</th><th>Duration</th><th>Students</th><th>Date Added</th></tr></thead><tbody>
                <?php foreach ($courses as $course): ?>
                <tr><td><?= (int)$course['id'] ?></td><td><?= htmlspecialchars($course['course_name']) ?></td><td><?= htmlspecialchars($course['department']) ?></td><td><?= htmlspecialchars($course['duration']) ?></td><td><span class="badge text-bg-info"><?= (int)$course['student_count'] ?></span></td><td><?= htmlspecialchars(date('d M Y', strtotime($course['created_at']))) ?></td></tr>
                <?php endforeach; ?>
                <?php if (!$courses): ?><tr><td colspan="6" class="text-center">No courses found.</td></tr><?php endif; ?>
                </tbody>
            </table></div>
        </div>
    </div>
</div>
<?php require __DIR__ . "/include/footer.php"; ?>
