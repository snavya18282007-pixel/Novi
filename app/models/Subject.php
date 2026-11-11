<?php
/**
 * Subject Model
 * Handles database operations for subject management and subject-wise metrics.
 */

require_once __DIR__ . '/../../config/database.php';

class Subject {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all subjects with task counts and completion percentage
     */
    public function getAll(): array {
        $sql = "SELECT 
                    s.*,
                    COUNT(t.id) AS total_tasks,
                    SUM(CASE WHEN t.status = 'Completed' THEN 1 ELSE 0 END) AS completed_tasks,
                    SUM(CASE WHEN t.status = 'Pending' THEN 1 ELSE 0 END) AS pending_tasks,
                    ROUND(
                        IF(COUNT(t.id) > 0, 
                           (SUM(CASE WHEN t.status = 'Completed' THEN 1 ELSE 0 END) / COUNT(t.id)) * 100, 
                           0
                        )
                    ) AS completion_percentage
                FROM subjects s
                LEFT JOIN tasks t ON s.id = t.subject_id
                GROUP BY s.id
                ORDER BY s.name ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Find single subject by ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM subjects WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $subject = $stmt->fetch();
        return $subject ?: null;
    }

    /**
     * Create a new subject
     */
    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO subjects (name, description, color) VALUES (:name, :description, :color)"
        );
        return $stmt->execute([
            'name'        => trim($data['name']),
            'description' => trim($data['description'] ?? ''),
            'color'       => $data['color'] ?? '#4f46e5'
        ]);
    }

    /**
     * Update existing subject
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE subjects SET name = :name, description = :description, color = :color WHERE id = :id"
        );
        return $stmt->execute([
            'id'          => $id,
            'name'        => trim($data['name']),
            'description' => trim($data['description'] ?? ''),
            'color'       => $data['color'] ?? '#4f46e5'
        ]);
    }

    /**
     * Delete a subject
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM subjects WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get total subject count
     */
    public function getTotalCount(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM subjects");
        return (int) $stmt->fetchColumn();
    }
}
