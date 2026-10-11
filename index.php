<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Track & Design | Employee Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <div class="bg-orb orb-a"></div>
  <div class="bg-orb orb-b"></div>

  <main class="container py-4 py-lg-5">
    <section class="hero-card p-4 p-lg-5 mb-4">
      <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
          <span class="badge text-bg-info mb-2">INTECH 3112 - Track & Design</span>
          <h1 class="display-6 fw-bold mb-2">Employee Management System</h1>
          <p class="text-light-subtle mb-0">Employee records, attendance, and reviews in one simple dashboard.</p>
        </div>
        <a href="admin.php" class="btn btn-outline-light">Open Admin Monitor</a>
      </div>
    </section>

    <section class="row g-4 mb-4">
      <div class="col-lg-4">
        <article class="glass p-3 h-100">
          <h2 class="h5 mb-1">Employees</h2>
          <p class="text-light-subtle mb-0">Add, edit, view, and remove staff records.</p>
        </article>
      </div>
      <div class="col-lg-4">
        <article class="glass p-3 h-100">
          <h2 class="h5 mb-1">Attendance</h2>
          <p class="text-light-subtle mb-0">Track daily time in, time out, and status.</p>
        </article>
      </div>
      <div class="col-lg-4">
        <article class="glass p-3 h-100">
          <h2 class="h5 mb-1">Reviews</h2>
          <p class="text-light-subtle mb-0">Save reviewer notes, score, and review date.</p>
        </article>
      </div>
    </section>

    <section class="glass p-4 mb-4">
      <h2 class="h4 mb-3">Employees</h2>
      <div id="employee-alert" class="inline-alert" role="alert"></div>
      <form id="employee-form" class="row g-3">
        <input type="hidden" id="employee-id" />
        <div class="col-md-6">
          <label class="form-label" for="full-name">Full Name</label>
          <input id="full-name" class="form-control" required />
        </div>
        <div class="col-md-6">
          <label class="form-label" for="email">Email</label>
          <input id="email" type="email" class="form-control" required />
        </div>
        <div class="col-md-4">
          <label class="form-label" for="position">Position</label>
          <input id="position" class="form-control" required />
        </div>
        <div class="col-md-4">
          <label class="form-label" for="department">Department</label>
          <input id="department" class="form-control" required />
        </div>
        <div class="col-md-4">
          <label class="form-label" for="date-hired">Date Hired</label>
          <input id="date-hired" type="date" class="form-control" required />
        </div>
        <div class="col-md-4">
          <label class="form-label" for="employee-status">Status</label>
          <select id="employee-status" class="form-select">
            <option>Active</option>
            <option>Probation</option>
            <option>Inactive</option>
          </select>
        </div>
        <div class="col-12 d-flex flex-wrap gap-2">
          <button type="submit" class="btn btn-info">Save</button>
          <button type="button" id="employee-reset" class="btn btn-outline-light">Clear</button>
        </div>
      </form>

      <div class="table-responsive mt-4">
        <table class="table table-dark table-hover align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Position</th>
              <th>Department</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="employees-body"></tbody>
        </table>
      </div>
    </section>

    <section class="glass p-4 mb-4">
      <h2 class="h4 mb-3">Attendance</h2>
      <div id="attendance-alert" class="inline-alert" role="alert"></div>
      <form id="attendance-form" class="row g-3">
        <input type="hidden" id="attendance-id" />
        <div class="col-md-4">
          <label class="form-label" for="attendance-employee">Employee</label>
          <select id="attendance-employee" class="form-select" required></select>
        </div>
        <div class="col-md-3">
          <label class="form-label" for="attendance-date">Date</label>
          <input id="attendance-date" type="date" class="form-control" required />
        </div>
        <div class="col-md-3">
          <label class="form-label" for="attendance-status">Status</label>
          <select id="attendance-status" class="form-select">
            <option>Present</option>
            <option>Late</option>
            <option>Absent</option>
            <option>On Leave</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label" for="time-in">Time In</label>
          <input id="time-in" type="time" class="form-control" />
        </div>
        <div class="col-md-3">
          <label class="form-label" for="time-out">Time Out</label>
          <input id="time-out" type="time" class="form-control" />
        </div>
        <div class="col-md-6">
          <label class="form-label" for="attendance-notes">Notes</label>
          <input id="attendance-notes" class="form-control" placeholder="Optional notes" />
        </div>
        <div class="col-12 d-flex flex-wrap gap-2">
          <button type="submit" class="btn btn-info">Save</button>
          <button type="button" id="attendance-reset" class="btn btn-outline-light">Clear</button>
        </div>
      </form>

      <div class="table-responsive mt-4">
        <table class="table table-dark table-hover align-middle">
          <thead>
            <tr>
              <th>Employee</th>
              <th>Date</th>
              <th>Status</th>
              <th>Time In</th>
              <th>Time Out</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="attendance-body"></tbody>
        </table>
      </div>
    </section>

    <section class="glass p-4 mb-4">
      <h2 class="h4 mb-3">Reviews</h2>
      <div id="review-alert" class="inline-alert" role="alert"></div>
      <form id="review-form" class="row g-3">
        <input type="hidden" id="review-id" />
        <div class="col-md-4">
          <label class="form-label" for="review-employee">Employee</label>
          <select id="review-employee" class="form-select" required></select>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="reviewer">Reviewer</label>
          <select id="reviewer" class="form-select"></select>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="review-period">Review Period</label>
          <input id="review-period" class="form-control" placeholder="Q1 2026" pattern=".*\d{4}.*" title="Include the year, e.g. Q1 2026" required />
        </div>
        <div class="col-md-3">
          <label class="form-label" for="review-score">Score (0-100)</label>
          <input id="review-score" type="number" min="0" max="100" step="0.01" class="form-control" required />
        </div>
        <div class="col-md-3">
          <label class="form-label" for="review-date">Review Date</label>
          <input id="review-date" type="date" class="form-control" required />
        </div>
        <div class="col-md-6">
          <label class="form-label" for="review-remarks">Remarks</label>
          <input id="review-remarks" class="form-control" placeholder="Performance summary" />
        </div>
        <div class="col-12 d-flex flex-wrap gap-2">
          <button type="submit" class="btn btn-info">Save</button>
          <button type="button" id="review-reset" class="btn btn-outline-light">Clear</button>
        </div>
      </form>

      <div class="table-responsive mt-4">
        <table class="table table-dark table-hover align-middle">
          <thead>
            <tr>
              <th>Employee</th>
              <th>Reviewer</th>
              <th>Period</th>
              <th>Score</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="reviews-body"></tbody>
        </table>
      </div>
    </section>
  </main>

  <script src="assets/app.js"></script>
</body>
</html>
