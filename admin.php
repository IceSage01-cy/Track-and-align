<?php

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/classes/Employee.php';
require_once __DIR__ . '/classes/Attendance.php';
require_once __DIR__ . '/classes/Review.php';

$employeeTotal = 0;
$presentToday = 0;
$averageScore = 0;
$latestAttendance = [];
$latestReviews = [];
$error = '';

try {
    $db = new Database();
    $connection = $db->getConnection();

    $employeeClass = new Employee($connection);
    $attendanceClass = new Attendance($connection);
    $reviewClass = new Review($connection);

    $employeeTotal = $employeeClass->countAll();
    $presentToday = $attendanceClass->countPresentToday();
    $averageScore = $reviewClass->averageScore();

    $latestAttendance = array_slice($attendanceClass->getAll(), 0, 8);
    $latestReviews = array_slice($reviewClass->getAll(), 0, 8);
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Track & Design | Admin Monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
    <main class="container py-4 py-lg-5">
        <section class="hero-card p-4 p-lg-5 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="badge text-bg-primary mb-2">Admin Monitoring Page</span>
                    <h1 class="h2 mb-1">Track & Design Control Center</h1>
                    <p class="text-light-subtle mb-0">This page monitors the whole website data as required by your project guide.</p>
                </div>
                <a href="index.php" class="btn btn-outline-light">Back to Main System</a>
            </div>
        </section>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger">Database error: <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <section class="row g-4 mb-4">
            <div class="col-md-4">
                <article class="glass p-4 h-100">
                    <h2 class="h6 text-light-subtle mb-1">Total Employees</h2>
                    <p class="display-6 fw-bold mb-0"><?= (int) $employeeTotal ?></p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="glass p-4 h-100">
                    <h2 class="h6 text-light-subtle mb-1">Present Today</h2>
                    <p class="display-6 fw-bold mb-0"><?= (int) $presentToday ?></p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="glass p-4 h-100">
                    <h2 class="h6 text-light-subtle mb-1">Average Review Score</h2>
                    <p class="display-6 fw-bold mb-0"><?= number_format($averageScore, 2) ?></p>
                </article>
            </div>
        </section>

        <section class="glass p-4 mb-4">
            <h2 class="h4 mb-3">Latest Attendance Logs</h2>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestAttendance as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['attendance_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($row['time_in'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($row['time_out'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="glass p-4">
            <h2 class="h4 mb-3">Latest Performance Reviews</h2>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Reviewer</th>
                            <th>Period</th>
                            <th>Score</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestReviews as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['employee_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($row['reviewer_name'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['review_period'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $row['score'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['review_date'], ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
