<?php

class Employee
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $stmt = $this->connection->query(
            'SELECT id, full_name, email, position, department, date_hired, status, created_at
             FROM employees
             ORDER BY created_at DESC'
        );

        return $stmt->fetchAll();
    }

    public function existsByName(string $fullName, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM employees WHERE LOWER(full_name) = LOWER(:full_name)';
        $params = [':full_name' => $fullName];

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
        $sql = 'INSERT INTO employees (full_name, email, position, department, date_hired, status)
                VALUES (:full_name, :email, :position, :department, :date_hired, :status)';

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':position' => $data['position'],
            ':department' => $data['department'],
            ':date_hired' => $data['date_hired'],
            ':status' => $data['status'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE employees
                SET full_name = :full_name,
                    email = :email,
                    position = :position,
                    department = :department,
                    date_hired = :date_hired,
                    status = :status
                WHERE id = :id';

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':position' => $data['position'],
            ':department' => $data['department'],
            ':date_hired' => $data['date_hired'],
            ':status' => $data['status'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare('DELETE FROM employees WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public function countAll(): int
    {
        $stmt = $this->connection->query('SELECT COUNT(*) AS total FROM employees');
        $row = $stmt->fetch();

        return (int) $row['total'];
    }
}
