<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Review.php';

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
    $review = new Review($db->getConnection());

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        respond(200, ['success' => true, 'data' => $review->getAll()]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        respond(405, ['success' => false, 'message' => 'Method not allowed']);
    }

    $data = payload();
    $action = $data['action'] ?? 'create';

    if ($action === 'delete') {
        $ok = $review->delete((int) ($data['id'] ?? 0));
        respond(200, ['success' => $ok, 'message' => $ok ? 'Review deleted.' : 'Delete failed.']);
    }

    $record = [
        'employee_id' => (int) ($data['employee_id'] ?? 0),
        'reviewer_id' => (int) ($data['reviewer_id'] ?? 0),
        'review_period' => trim((string) ($data['review_period'] ?? '')),
        'score' => (float) ($data['score'] ?? 0),
        'remarks' => trim((string) ($data['remarks'] ?? '')),
        'review_date' => (string) ($data['review_date'] ?? ''),
    ];

    if ($record['employee_id'] === 0 || $record['review_date'] === '') {
        respond(422, ['success' => false, 'message' => 'Employee and review date are required.']);
    }

    if ($record['review_period'] === '' || !preg_match('/\d{4}/', $record['review_period'])) {
        $date = new DateTimeImmutable($record['review_date']);
        $record['review_period'] = sprintf('Q%d %d', (int) ceil(((int) $date->format('n')) / 3), (int) $date->format('Y'));
    }

    $date = new DateTimeImmutable($record['review_date']);
    $expectedPeriod = sprintf('Q%d %d', (int) ceil(((int) $date->format('n')) / 3), (int) $date->format('Y'));

    if (strtolower(trim($record['review_period'])) !== strtolower(trim($expectedPeriod))) {
        respond(422, ['success' => false, 'message' => 'Review period must match the selected review date, such as Q1 2026.']);
    }

    if ($review->existsForEmployeeAndPeriod($record['employee_id'], $record['reviewer_id'] ?: null, $record['review_period'], $action === 'update' ? (int) ($data['id'] ?? 0) : null)) {
        respond(409, ['success' => false, 'message' => 'A review already exists for this employee from the same reviewer in the selected period.']);
    }

    if ($action === 'update') {
        $ok = $review->update((int) ($data['id'] ?? 0), $record);
        respond(200, ['success' => $ok, 'message' => $ok ? 'Review updated.' : 'Update failed.']);
    }

    $ok = $review->create($record);
    respond(201, ['success' => $ok, 'message' => $ok ? 'Review created.' : 'Create failed.']);
} catch (Throwable $error) {
    respond(500, ['success' => false, 'message' => $error->getMessage()]);
}
