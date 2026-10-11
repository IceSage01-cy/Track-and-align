<?php

class Attendance
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $sql = 'SELECT a.id, a.employee_id, e.full_name, a.attendance_date, a.status, a.time_in, a.time_out, a.notes
                FROM attendance_logs a
                INNER JOIN employees e ON e.id = a.employee_id
                ORDER BY a.attendance_date DESC, a.id DESC';

        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }

    public function existsForDate(int $employeeId, string $attendanceDate, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM attendance_logs WHERE employee_id = :employee_id AND attendance_date = :attendance_date';
        $params = [
            ':employee_id' => $employeeId,
            ':attendance_date' => $attendanceDate,
        ];

        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude_id';
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch() !== false;
    }

    public function create(array $data): bool
    {
        $sql = 'INSERT INTO attendance_logs (employee_id, attendance_date, status, time_in, time_out, notes)
                VALUES (:employee_id, :attendance_date, :status, :time_in, :time_out, :notes)';

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':employee_id' => $data['employee_id'],
            ':attendance_date' => $data['attendance_date'],
            ':status' => $data['status'],
            ':time_in' => $data['time_in'],
            ':time_out' => $data['time_out'],
            ':notes' => $data['notes'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE attendance_logs
                SET employee_id = :employee_id,
                    attendance_date = :attendance_date,
                    status = :status,
                    time_in = :time_in,
                    time_out = :time_out,
                    notes = :notes
                WHERE id = :id';

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':employee_id' => $data['employee_id'],
            ':attendance_date' => $data['attendance_date'],
            ':status' => $data['status'],
            ':time_in' => $data['time_in'],
            ':time_out' => $data['time_out'],
            ':notes' => $data['notes'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare('DELETE FROM attendance_logs WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public function countPresentToday(): int
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM attendance_logs
                WHERE attendance_date = CURDATE() AND status = "Present"';

        $stmt = $this->connection->query($sql);
        $row = $stmt->fetch();

        return (int) $row['total'];
    }
}
