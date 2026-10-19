<?php
require __DIR__ . "/config/database.php";
require __DIR__ . "/include/header.php";

$all      = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$total    = count($all);
$active   = count(array_filter($all, fn($s) => $s['status'] === 'Active'));
$inactive = $total - $active;
$pct      = $total ? round($active / $total * 100) : 0;

// Students per course (for progress bars)
$courses = [];
foreach ($all as $s) { $courses[$s['course']] = ($courses[$s['course']] ?? 0) + 1; }
arsort($courses);

// Pagination for "All" tab
$perPage = 5;
$page    = max(1, (int)($_GET['page'] ?? 1));
$pages   = max(1, (int)ceil($total / $perPage));
$page    = min($page, $pages);
$paged   = array_slice($all, ($page - 1) * $perPage, $perPage);

function student_rows(array $rows) {
    foreach ($rows as $s) {
        $badge = $s['status'] === 'Active' ? 'success' : 'secondary';
        echo "<tr><td>{$s['id']}</td><td>" . htmlspecialchars($s['name']) . "</td><td>" . htmlspecialchars($s['email'])
           . "</td><td>" . htmlspecialchars($s['course']) . "</td><td><span class='badge text-bg-$badge'>{$s['status']}</span></td></tr>";
    }
    if (!$rows) echo "<tr><td colspan='5' class='text-center'>No students.</td></tr>";
}
?>
<!-- Breadcrumb -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Dashboard</h3>
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</div>

<div class="mb-3 d-flex gap-2 flex-wrap">
    <a class="btn btn-outline-primary btn-sm" href="<?= BASE_URL ?>analytics.php"><i class="bi bi-bar-chart me-1"></i>Open Charts & Filters</a>
    <a class="btn btn-outline-success btn-sm" href="<?= BASE_URL ?>courses.php"><i class="bi bi-journal-bookmark me-1"></i>Manage Courses</a>
</div>

<!-- Callout -->
<div class="callout callout-info border-start border-4 border-info bg-body p-3 mb-3">
    <h5>Welcome, Admin!</h5>
    <p class="mb-0">You have <strong><?= $total ?></strong> students, <strong><?= $active ?></strong> of them active.</p>
</div>

<!-- Stat boxes + buttons -->
<div class="row">
    <div class="col-md-4"><div class="small-box text-bg-primary">
        <div class="inner"><h3><?= $total ?></h3><p>Total Students</p></div>
        <a href="<?= BASE_URL ?>students/read.php" class="small-box-footer link-light p-2 d-block">View all <i class="bi bi-arrow-right"></i></a>
    </div></div>
    <div class="col-md-4"><div class="small-box text-bg-success">
        <div class="inner"><h3><?= $active ?></h3><p>Active</p></div>
        <span class="small-box-footer p-2 d-block">&nbsp;</span>
    </div></div>
    <div class="col-md-4"><div class="small-box text-bg-secondary">
        <div class="inner"><h3><?= $inactive ?></h3><p>Inactive</p></div>
        <span class="small-box-footer p-2 d-block">&nbsp;</span>
    </div></div>
</div>

<div class="row">
    <!-- Progress bars -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title">Progress</h5></div>
            <div class="card-body">
                <p class="mb-1">Active students (<?= $pct ?>%)</p>
                <div class="progress mb-3"><div class="progress-bar bg-success" style="width: <?= $pct ?>%"></div></div>
                <?php foreach ($courses as $name => $count): $p = round($count / max($total, 1) * 100); ?>
                    <p class="mb-1"><?= htmlspecialchars($name) ?> <span class="float-end"><?= $count ?></span></p>
                    <div class="progress mb-3"><div class="progress-bar progress-bar-striped" style="width: <?= $p ?>%"></div></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Tabs + table + pagination -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-all" type="button">All <span class="badge text-bg-primary"><?= $total ?></span></button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-active" type="button">Active <span class="badge text-bg-success"><?= $active ?></span></button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-inactive" type="button">Inactive <span class="badge text-bg-secondary"><?= $inactive ?></span></button></li>
                    </ul>
                    <!-- Dropdown + modal button -->
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal"><i class="bi bi-plus-lg"></i> Quick Add</button>
                        <button class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>students/read.php">Manage students</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>students/create.php">Full add form</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Input group -->
                <div class="input-group p-2">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="studentSearch" class="form-control" placeholder="Filter students...">
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-all">
                        <table class="table table-striped mb-0 searchable">
                            <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Course</th><th>Status</th></tr></thead>
                            <tbody><?php student_rows($paged); ?></tbody>
                        </table>
                        <div class="p-2"><ul class="pagination pagination-sm m-0 float-end">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $pages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page + 1 ?>">&raquo;</a></li>
                        </ul></div>
                    </div>
                    <div class="tab-pane fade" id="tab-active">
                        <table class="table table-striped mb-0 searchable"><tbody><?php student_rows(array_filter($all, fn($s) => $s['status'] === 'Active')); ?></tbody></table>
                    </div>
                    <div class="tab-pane fade" id="tab-inactive">
                        <table class="table table-striped mb-0 searchable"><tbody><?php student_rows(array_filter($all, fn($s) => $s['status'] === 'Inactive')); ?></tbody></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: quick add student (posts to create.php) -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="<?= BASE_URL ?>students/create.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Course</label><input type="text" name="course" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Status</label>
                    <select name="status" class="form-select"><option>Active</option><option>Inactive</option></select></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('studentSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('table.searchable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
<?php require __DIR__ . "/include/footer.php"; ?>
