<?php

class Review
{
    public function __construct(private PDO $connection)
    {
    }

    public function getAll(): array
    {
        $sql = 'SELECT r.id,
                       r.employee_id,
                       e.full_name AS employee_name,
                       r.reviewer_id,
                       rv.full_name AS reviewer_name,
                       r.review_period,
                       r.score,
                       r.remarks,
                       r.review_date
                FROM performance_reviews r
                INNER JOIN employees e ON e.id = r.employee_id
                LEFT JOIN employees rv ON rv.id = r.reviewer_id
                ORDER BY r.review_date DESC, r.id DESC';

        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }

    public function existsForEmployeeAndPeriod(int $employeeId, ?int $reviewerId, string $reviewPeriod, ?int $excludeId = null): bool
    {
        $sql = 'SELECT 1 FROM performance_reviews WHERE employee_id = :employee_id AND reviewer_id = :reviewer_id AND review_period = :review_period';

        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude_id';
        }

        $stmt = $this->connection->prepare($sql);
        $params = [
            ':employee_id' => $employeeId,
            ':reviewer_id' => $reviewerId,
            ':review_period' => $reviewPeriod,
        ];

        if ($excludeId !== null) {
            $params[':exclude_id'] = $excludeId;
        }

        $stmt->execute($params);

        return $stmt->fetchColumn() !== false;
    }

    public function create(array $data): bool
    {
        $sql = 'INSERT INTO performance_reviews (employee_id, reviewer_id, review_period, score, remarks, review_date)
                VALUES (:employee_id, :reviewer_id, :review_period, :score, :remarks, :review_date)';

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':employee_id' => $data['employee_id'],
            ':reviewer_id' => $data['reviewer_id'] ?: null,
            ':review_period' => $data['review_period'],
            ':score' => $data['score'],
            ':remarks' => $data['remarks'],
            ':review_date' => $data['review_date'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE performance_reviews
                SET employee_id = :employee_id,
                    reviewer_id = :reviewer_id,
                    review_period = :review_period,
                    score = :score,
                    remarks = :remarks,
                    review_date = :review_date
                WHERE id = :id';

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':employee_id' => $data['employee_id'],
            ':reviewer_id' => $data['reviewer_id'] ?: null,
            ':review_period' => $data['review_period'],
            ':score' => $data['score'],
            ':remarks' => $data['remarks'],
            ':review_date' => $data['review_date'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare('DELETE FROM performance_reviews WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public function averageScore(): float
    {
        $stmt = $this->connection->query('SELECT AVG(score) AS average_score FROM performance_reviews');
        $row = $stmt->fetch();

        return (float) ($row['average_score'] ?? 0);
    }
}
