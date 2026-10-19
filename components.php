<?php
// Shared form component for create/update student pages.
function student_form(array $s = [], string $button = "Save") { ?>
    <form method="post">
        <div class="mb-3"><label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($s['name'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($s['email'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label">Course</label>
            <input type="text" name="course" class="form-control" required value="<?= htmlspecialchars($s['course'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['Active', 'Inactive'] as $opt): ?>
                    <option <?= ($s['status'] ?? 'Active') === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select></div>
        <button class="btn btn-primary"><?= $button ?></button>
        <a href="<?= BASE_URL ?>students/read.php" class="btn btn-secondary">Cancel</a>
    </form>
<?php }
