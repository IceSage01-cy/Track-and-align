const employeeForm = document.getElementById('employee-form');
const employeeId = document.getElementById('employee-id');
const fullName = document.getElementById('full-name');
const email = document.getElementById('email');
const position = document.getElementById('position');
const department = document.getElementById('department');
const dateHired = document.getElementById('date-hired');
const employeeStatus = document.getElementById('employee-status');
const employeesBody = document.getElementById('employees-body');

const attendanceForm = document.getElementById('attendance-form');
const attendanceId = document.getElementById('attendance-id');
const attendanceEmployee = document.getElementById('attendance-employee');
const attendanceDate = document.getElementById('attendance-date');
const attendanceStatus = document.getElementById('attendance-status');
const timeIn = document.getElementById('time-in');
const timeOut = document.getElementById('time-out');
const attendanceNotes = document.getElementById('attendance-notes');
const attendanceBody = document.getElementById('attendance-body');

const reviewForm = document.getElementById('review-form');
const reviewId = document.getElementById('review-id');
const reviewEmployee = document.getElementById('review-employee');
const reviewer = document.getElementById('reviewer');
const reviewPeriod = document.getElementById('review-period');
const reviewScore = document.getElementById('review-score');
const reviewDate = document.getElementById('review-date');
const reviewRemarks = document.getElementById('review-remarks');
const reviewsBody = document.getElementById('reviews-body');

let employeesCache = [];
let attendanceCache = [];
let reviewsCache = [];

function getRowMarkup(items, rowTemplate, emptyLabel) {
  return items.length
    ? items.map(rowTemplate).join('')
    : `<tr><td colspan="6" class="text-center text-light-subtle py-4">No ${emptyLabel} yet.</td></tr>`;
}

function setSelectOptions(element, items, placeholder) {
  element.innerHTML = [
    `<option value="">${placeholder}</option>`,
    ...items.map((item) => `<option value="${item.id}">${item.full_name}</option>`),
  ].join('');
}

function clearAlerts() {
  document.querySelectorAll('.inline-alert').forEach((box) => {
    box.textContent = '';
    box.classList.remove('show', 'alert-success', 'alert-danger');
  });
}

function showAlert(message, type = 'success', target = 'employee') {
  clearAlerts();

  const targetMap = {
    employee: document.getElementById('employee-alert'),
    attendance: document.getElementById('attendance-alert'),
    review: document.getElementById('review-alert'),
  };

  const box = targetMap[target];

  if (!box) return;

  box.className = `inline-alert alert-${type}`;
  box.textContent = message;
  box.classList.add('show');

  const form = box.closest('section');
  if (form) {
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  if (type === 'success') {
    setTimeout(() => {
      box.classList.remove('show');
    }, 2600);
  }
}

async function request(url, method = 'GET', payload = null) {
  const options = { method };

  if (payload) {
    options.headers = { 'Content-Type': 'application/json' };
    options.body = JSON.stringify(payload);
  }

  const response = await fetch(url, options);
  const data = await response.json();

  if (!response.ok || data.success === false) {
    throw new Error(data.message || 'Request failed.');
  }

  return data;
}

function resetEmployeeForm() {
  employeeId.value = '';
  employeeForm.reset();
}

function resetAttendanceForm() {
  attendanceId.value = '';
  attendanceForm.reset();
}

function resetReviewForm() {
  reviewId.value = '';
  reviewForm.reset();
}

function populateEmployeeDropdowns() {
  setSelectOptions(attendanceEmployee, employeesCache, 'Select employee');
  setSelectOptions(reviewEmployee, employeesCache, 'Select employee');
  setSelectOptions(reviewer, employeesCache, 'No reviewer');
}

function getReviewPeriodFromDate(dateValue) {
  if (!dateValue) {
    return '';
  }

  const date = new Date(`${dateValue}T00:00:00`);
  if (Number.isNaN(date.getTime())) {
    return '';
  }

  const year = date.getFullYear();
  const month = date.getMonth() + 1;
  const quarter = Math.ceil(month / 3);

  return `Q${quarter} ${year}`;
}

function reviewPeriodMatchesDate(periodValue, dateValue) {
  if (!periodValue || !dateValue) {
    return false;
  }

  const normalized = periodValue.trim();
  const expected = getReviewPeriodFromDate(dateValue);

  return normalized.toLowerCase() === expected.toLowerCase();
}

function syncReviewPeriodFromDate() {
  if (!reviewDate.value) {
    return;
  }

  const current = reviewPeriod.value.trim();
  const inferred = getReviewPeriodFromDate(reviewDate.value);

  if (!current || !/\d{4}/.test(current) || !reviewPeriodMatchesDate(current, reviewDate.value)) {
    reviewPeriod.value = inferred;
  }
}

function renderEmployees() {
  employeesBody.innerHTML = getRowMarkup(employeesCache, (employee) => `
    <tr>
      <td>${employee.full_name}</td>
      <td>${employee.email}</td>
      <td>${employee.position}</td>
      <td>${employee.department}</td>
      <td>${employee.status}</td>
      <td>
        <button class="action-btn action-edit" data-type="employee-edit" data-id="${employee.id}">Edit</button>
        <button class="action-btn action-delete" data-type="employee-delete" data-id="${employee.id}">Delete</button>
      </td>
    </tr>
  `, 'employees');
}

function renderAttendance() {
  attendanceBody.innerHTML = getRowMarkup(attendanceCache, (entry) => `
    <tr>
      <td>${entry.full_name}</td>
      <td>${entry.attendance_date}</td>
      <td>${entry.status}</td>
      <td>${entry.time_in || '-'}</td>
      <td>${entry.time_out || '-'}</td>
      <td>
        <button class="action-btn action-edit" data-type="attendance-edit" data-id="${entry.id}">Edit</button>
        <button class="action-btn action-delete" data-type="attendance-delete" data-id="${entry.id}">Delete</button>
      </td>
    </tr>
  `, 'attendance records');
}

function renderReviews() {
  reviewsBody.innerHTML = getRowMarkup(reviewsCache, (entry) => `
    <tr>
      <td>${entry.employee_name}</td>
      <td>${entry.reviewer_name || 'N/A'}</td>
      <td>${entry.review_period}</td>
      <td>${entry.score}</td>
      <td>${entry.review_date}</td>
      <td>
        <button class="action-btn action-edit" data-type="review-edit" data-id="${entry.id}">Edit</button>
        <button class="action-btn action-delete" data-type="review-delete" data-id="${entry.id}">Delete</button>
      </td>
    </tr>
  `, 'reviews');
}

async function loadAll() {
  await Promise.all([loadEmployees(), loadAttendance(), loadReviews()]);
}

async function loadEmployees() {
  const response = await request('api/employees.php');
  employeesCache = response.data;
  renderEmployees();
  populateEmployeeDropdowns();
}

async function loadAttendance() {
  const response = await request('api/attendance.php');
  attendanceCache = response.data;
  renderAttendance();
}

async function loadReviews() {
  const response = await request('api/reviews.php');
  reviewsCache = response.data;
  renderReviews();
}

employeeForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  try {
    const payload = {
      action: employeeId.value ? 'update' : 'create',
      id: employeeId.value,
      full_name: fullName.value,
      email: email.value,
      position: position.value,
      department: department.value,
      date_hired: dateHired.value,
      status: employeeStatus.value,
    };

    const response = await request('api/employees.php', 'POST', payload);
    showAlert(response.message || 'Employee saved.', 'success', 'employee');
    resetEmployeeForm();
    await loadAll();
  } catch (error) {
    showAlert(error.message, 'danger', 'employee');
  }
});

attendanceForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  try {
    const payload = {
      action: attendanceId.value ? 'update' : 'create',
      id: attendanceId.value,
      employee_id: attendanceEmployee.value,
      attendance_date: attendanceDate.value,
      status: attendanceStatus.value,
      time_in: timeIn.value,
      time_out: timeOut.value,
      notes: attendanceNotes.value,
    };

    const response = await request('api/attendance.php', 'POST', payload);
    showAlert(response.message || 'Attendance saved.', 'success', 'attendance');
    resetAttendanceForm();
    await loadAttendance();
  } catch (error) {
    showAlert(error.message, 'danger', 'attendance');
  }
});

reviewDate.addEventListener('change', syncReviewPeriodFromDate);
reviewPeriod.addEventListener('blur', () => {
  const value = reviewPeriod.value.trim();
  const inferred = getReviewPeriodFromDate(reviewDate.value);

  if (!value || !/\d{4}/.test(value) || !reviewPeriodMatchesDate(value, reviewDate.value)) {
    reviewPeriod.value = inferred;
  }
});

reviewForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  try {
    const reviewPeriodValue = reviewPeriod.value.trim();
    const inferredPeriod = getReviewPeriodFromDate(reviewDate.value);
    const finalReviewPeriod = reviewPeriodValue && /\d{4}/.test(reviewPeriodValue)
      ? reviewPeriodValue
      : inferredPeriod;

    if (!finalReviewPeriod) {
      throw new Error('Review period must include a year, such as Q1 2026.');
    }

    if (!reviewPeriodMatchesDate(finalReviewPeriod, reviewDate.value)) {
      reviewPeriod.value = inferredPeriod;
      throw new Error('Review period must match the selected review date, such as Q1 2026.');
    }

    reviewPeriod.value = finalReviewPeriod;

    const payload = {
      action: reviewId.value ? 'update' : 'create',
      id: reviewId.value,
      employee_id: reviewEmployee.value,
      reviewer_id: reviewer.value,
      review_period: finalReviewPeriod,
      score: reviewScore.value,
      remarks: reviewRemarks.value,
      review_date: reviewDate.value,
    };

    const response = await request('api/reviews.php', 'POST', payload);
    showAlert(response.message || 'Review saved.', 'success', 'review');
    resetReviewForm();
    await loadReviews();
  } catch (error) {
    showAlert(error.message, 'danger', 'review');
  }
});

document.getElementById('employee-reset').addEventListener('click', resetEmployeeForm);
document.getElementById('attendance-reset').addEventListener('click', resetAttendanceForm);
document.getElementById('review-reset').addEventListener('click', resetReviewForm);

document.addEventListener('click', async (event) => {
  const target = event.target.closest('[data-type]');

  if (!target) {
    return;
  }

  const id = Number(target.dataset.id || 0);
  const type = target.dataset.type;

  if (!id || !type) {
    return;
  }

  try {
    switch (type) {
      case 'employee-edit': {
        const employee = employeesCache.find((item) => Number(item.id) === id);
        if (!employee) return;

        employeeId.value = employee.id;
        fullName.value = employee.full_name;
        email.value = employee.email;
        position.value = employee.position;
        department.value = employee.department;
        dateHired.value = employee.date_hired;
        employeeStatus.value = employee.status;
        break;
      }
      case 'employee-delete':
        await request('api/employees.php', 'POST', { action: 'delete', id });
        showAlert('Employee deleted.', 'success', 'employee');
        await loadAll();
        break;
      case 'attendance-edit': {
        const attendance = attendanceCache.find((item) => Number(item.id) === id);
        if (!attendance) return;

        attendanceId.value = attendance.id;
        attendanceEmployee.value = attendance.employee_id;
        attendanceDate.value = attendance.attendance_date;
        attendanceStatus.value = attendance.status;
        timeIn.value = attendance.time_in || '';
        timeOut.value = attendance.time_out || '';
        attendanceNotes.value = attendance.notes || '';
        break;
      }
      case 'attendance-delete':
        await request('api/attendance.php', 'POST', { action: 'delete', id });
        showAlert('Attendance deleted.', 'success', 'attendance');
        await loadAttendance();
        break;
      case 'review-edit': {
        const review = reviewsCache.find((item) => Number(item.id) === id);
        if (!review) return;

        reviewId.value = review.id;
        reviewEmployee.value = review.employee_id;
        reviewer.value = review.reviewer_id || '';
        reviewPeriod.value = review.review_period;
        reviewScore.value = review.score;
        reviewDate.value = review.review_date;
        reviewRemarks.value = review.remarks || '';
        break;
      }
      case 'review-delete':
        await request('api/reviews.php', 'POST', { action: 'delete', id });
        showAlert('Review deleted.', 'success', 'review');
        await loadReviews();
        break;
    }
  } catch (error) {
    showAlert(error.message, 'danger', 'employee');
  }
});

async function boot() {
  try {
    await loadAll();
  } catch (error) {
    showAlert(error.message, 'danger', 'employee');
  }
}

boot();
