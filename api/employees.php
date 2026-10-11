<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Employee.php';

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
    $employee = new Employee($db->getConnection());

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        respond(200, ['success' => true, 'data' => $employee->getAll()]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        respond(405, ['success' => false, 'message' => 'Method not allowed']);
    }

    $data = payload();
    $action = $data['action'] ?? 'create';

    if ($action === 'delete') {
        $ok = $employee->delete((int) ($data['id'] ?? 0));
        respond(200, ['success' => $ok, 'message' => $ok ? 'Employee deleted.' : 'Delete failed.']);
    }

    $record = [
        'full_name' => trim((string) ($data['full_name'] ?? '')),
        'email' => trim((string) ($data['email'] ?? '')),
        'position' => trim((string) ($data['position'] ?? '')),
        'department' => trim((string) ($data['department'] ?? '')),
        'date_hired' => (string) ($data['date_hired'] ?? ''),
        'status' => trim((string) ($data['status'] ?? 'Active')),
    ];

    if ($record['full_name'] === '' || $record['email'] === '') {
        respond(422, ['success' => false, 'message' => 'Name and email are required.']);
    }

    if ($employee->existsByName($record['full_name'], $action === 'update' ? (int) ($data['id'] ?? 0) : null)) {
        respond(409, ['success' => false, 'message' => 'Employee name already exists.']);
    }

    if ($action === 'update') {
        $ok = $employee->update((int) ($data['id'] ?? 0), $record);
        respond(200, ['success' => $ok, 'message' => $ok ? 'Employee updated.' : 'Update failed.']);
    }

    $ok = $employee->create($record);
    respond(201, ['success' => $ok, 'message' => $ok ? 'Employee created.' : 'Create failed.']);
} catch (Throwable $error) {
    respond(500, ['success' => false, 'message' => $error->getMessage()]);
}
