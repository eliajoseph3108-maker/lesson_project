<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= BASE_URL ?>dashboard.php" class="brand-link">
            <span class="brand-text fw-light">Student Web App</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" role="menu">
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>dashboard.php" class="nav-link"><i class="nav-icon bi bi-speedometer"></i><p>Dashboard</p></a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>students/read.php" class="nav-link"><i class="nav-icon bi bi-people"></i><p>Students</p></a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>students/create.php" class="nav-link"><i class="nav-icon bi bi-person-plus"></i><p>Add Student</p></a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>analytics.php" class="nav-link"><i class="nav-icon bi bi-bar-chart"></i><p>Analytics & Charts</p></a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>courses.php" class="nav-link"><i class="nav-icon bi bi-journal-bookmark"></i><p>Courses Table</p></a>
                </li>
            </ul>
        </nav>
    </div>
</aside>