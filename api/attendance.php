<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Attendance.php';

function respond(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function payload(): array
{
    $raw = file_get_contents('php://input');

    if (!empty($raw)) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    return $_POST;
}

try {
    $db = new Database();
    $attendance = new Attendance($db->getConnection());

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        respond(200, ['success' => true, 'data' => $attendance->getAll()]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        respond(405, ['success' => false, 'message' => 'Method not allowed']);
    }

    $data = payload();
    $action = $data['action'] ?? 'create';

    if ($action === 'delete') {
        $ok = $attendance->delete((int) ($data['id'] ?? 0));
        respond(200, ['success' => $ok, 'message' => $ok ? 'Attendance deleted.' : 'Delete failed.']);
    }

    $record = [
        'employee_id' => (int) ($data['employee_id'] ?? 0),
        'attendance_date' => (string) ($data['attendance_date'] ?? ''),
        'status' => trim((string) ($data['status'] ?? 'Present')),
        'time_in' => (string) ($data['time_in'] ?? null),
        'time_out' => (string) ($data['time_out'] ?? null),
        'notes' => trim((string) ($data['notes'] ?? '')),
    ];

    if ($record['employee_id'] === 0 || $record['attendance_date'] === '') {
        respond(422, ['success' => false, 'message' => 'Employee and attendance date are required.']);
    }

    if ($attendance->existsForDate($record['employee_id'], $record['attendance_date'], $action === 'update' ? (int) ($data['id'] ?? 0) : null)) {
        respond(409, ['success' => false, 'message' => 'Attendance for this employee on this date already exists.']);
    }

    if ($action === 'update') {
        $ok = $attendance->update((int) ($data['id'] ?? 0), $record);
        respond(200, ['success' => $ok, 'message' => $ok ? 'Attendance updated.' : 'Update failed.']);
    }

    $ok = $attendance->create($record);
    respond(201, ['success' => $ok, 'message' => $ok ? 'Attendance recorded.' : 'Create failed.']);
} catch (Throwable $error) {
    respond(500, ['success' => false, 'message' => $error->getMessage()]);
}
