<?php
require __DIR__ . "/config/database.php";

$statusFilter = $_GET['status'] ?? 'All';
$courseFilter = $_GET['course'] ?? 'All';
$allowedStatuses = ['All', 'Active', 'Inactive'];
if (!in_array($statusFilter, $allowedStatuses, true)) $statusFilter = 'All';

$courses = $pdo->query("SELECT course_name FROM courses ORDER BY course_name")->fetchAll(PDO::FETCH_COLUMN);
if ($courseFilter !== 'All' && !in_array($courseFilter, $courses, true)) $courseFilter = 'All';

$sql = "SELECT * FROM students WHERE 1=1";
$params = [];
if ($statusFilter !== 'All') { $sql .= " AND status = ?"; $params[] = $statusFilter; }
if ($courseFilter !== 'All') { $sql .= " AND course = ?"; $params[] = $courseFilter; }
$sql .= " ORDER BY name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$filteredStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$active = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status='Active'")->fetchColumn();
$inactive = $total - $active;
$courseCounts = $pdo->query("SELECT c.course_name, COUNT(s.id) AS total
    FROM courses c LEFT JOIN students s ON s.course = c.course_name
    GROUP BY c.id, c.course_name ORDER BY total DESC, c.course_name")->fetchAll(PDO::FETCH_ASSOC);
$maxCount = 1;
foreach ($courseCounts as $row) $maxCount = max($maxCount, (int)$row['total']);

require __DIR__ . "/include/header.php";
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h3 class="mb-1">Analytics & Charts</h3><p class="text-secondary mb-0">Student enrolment overview</p></div>
    <span class="badge text-bg-primary p-2">AdminLTE Components</span>
</div>

<div class="row">
    <div class="col-md-4"><div class="small-box text-bg-primary"><div class="inner"><h3><?= $total ?></h3><p>Total Students</p></div><div class="small-box-footer p-2">All registered students</div></div></div>
    <div class="col-md-4"><div class="small-box text-bg-success"><div class="inner"><h3><?= $active ?></h3><p>Active Students</p></div><div class="small-box-footer p-2">Currently active</div></div></div>
    <div class="col-md-4"><div class="small-box text-bg-secondary"><div class="inner"><h3><?= $inactive ?></h3><p>Inactive Students</p></div><div class="small-box-footer p-2">Currently inactive</div></div></div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h5 class="card-title"><i class="bi bi-bar-chart-line me-2"></i>Students by Course</h5></div>
            <div class="card-body">
                <?php if (!$courseCounts): ?><p class="text-secondary">No course data available.</p><?php endif; ?>
                <?php foreach ($courseCounts as $row):
                    $count = (int)$row['total'];
                    $width = round(($count / $maxCount) * 100);
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1"><span><?= htmlspecialchars($row['course_name']) ?></span><strong><?= $count ?></strong></div>
                    <div class="progress" style="height: 18px;"><div class="progress-bar" role="progressbar" style="width: <?= $width ?>%" aria-valuenow="<?= $count ?>" aria-valuemin="0" aria-valuemax="<?= $maxCount ?>"><?= $count ?></div></div>
                </div>
                <?php endforeach; ?>
                <small class="text-secondary">This bar chart is generated from the current database records.</small>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h5 class="card-title"><i class="bi bi-pie-chart me-2"></i>Status Overview</h5></div>
            <div class="card-body">
                <p class="mb-1">Active (<?= $active ?>)</p>
                <div class="progress mb-3" style="height: 22px;"><div class="progress-bar bg-success" style="width: <?= $total ? round($active/$total*100) : 0 ?>%"><?= $total ? round($active/$total*100) : 0 ?>%</div></div>
                <p class="mb-1">Inactive (<?= $inactive ?>)</p>
                <div class="progress mb-3" style="height: 22px;"><div class="progress-bar bg-secondary" style="width: <?= $total ? round($inactive/$total*100) : 0 ?>%"><?= $total ? round($inactive/$total*100) : 0 ?>%</div></div>
                <a href="<?= BASE_URL ?>students/read.php" class="btn btn-outline-primary btn-sm">View student records</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="card-title"><i class="bi bi-funnel me-2"></i>Select / Dropdown Filter</h5></div>
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-5"><label for="status" class="form-label">Student status</label>
                <select class="form-select" id="status" name="status">
                    <?php foreach ($allowedStatuses as $option): ?><option value="<?= htmlspecialchars($option) ?>" <?= $statusFilter === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5"><label for="course" class="form-label">Course</label>
                <select class="form-select" id="course" name="course"><option value="All" <?= $courseFilter === 'All' ? 'selected' : '' ?>>All courses</option>
                    <?php foreach ($courses as $option): ?><option value="<?= htmlspecialchars($option) ?>" <?= $courseFilter === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-grid"><button class="btn btn-primary" type="submit">Apply</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="card-title">Filtered Students <span class="badge text-bg-primary"><?= count($filteredStudents) ?></span></h5></div>
    <div class="table-responsive"><table class="table table-striped table-hover mb-0">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Status</th></tr></thead><tbody>
        <?php foreach ($filteredStudents as $student): ?>
        <tr><td><?= (int)$student['id'] ?></td><td><?= htmlspecialchars($student['name']) ?></td><td><?= htmlspecialchars($student['email']) ?></td><td><?= htmlspecialchars($student['course']) ?></td><td><span class="badge text-bg-<?= $student['status'] === 'Active' ? 'success' : 'secondary' ?>"><?= htmlspecialchars($student['status']) ?></span></td></tr>
        <?php endforeach; ?>
        <?php if (!$filteredStudents): ?><tr><td colspan="5" class="text-center text-secondary py-4">No students match these filters.</td></tr><?php endif; ?>
        </tbody></table></div>
</div>
<?php require __DIR__ . "/include/footer.php"; ?>
