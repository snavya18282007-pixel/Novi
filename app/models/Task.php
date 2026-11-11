<?php
/**
 * Task Model
 * Handles task management, filtering, statistics, and study goals.
 */

require_once __DIR__ . '/../../config/database.php';

class Task {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get tasks with optional filters, search, and sorting
     */
    public function getAll(array $filters = []): array {
        $sql = "SELECT 
                    t.*,
                    s.name AS subject_name,
                    s.color AS subject_color,
                    (CASE WHEN t.due_date < CURDATE() AND t.status != 'Completed' THEN 1 ELSE 0 END) AS is_overdue
                FROM tasks t
                JOIN subjects s ON t.subject_id = s.id
                WHERE 1=1";
        
        $params = [];

        // Apply status/due filter
        if (!empty($filters['filter'])) {
            switch ($filters['filter']) {
                case 'today':
                    $sql .= " AND t.due_date = CURDATE()";
                    break;
                case 'upcoming':
                    $sql .= " AND t.due_date > CURDATE() AND t.status = 'Pending'";
                    break;
                case 'completed':
                    $sql .= " AND t.status = 'Completed'";
                    break;
                case 'pending':
                    $sql .= " AND t.status = 'Pending'";
                    break;
                case 'overdue':
                    $sql .= " AND t.due_date < CURDATE() AND t.status != 'Completed'";
                    break;
            }
        }

        // Apply subject filter
        if (!empty($filters['subject_id'])) {
            $sql .= " AND t.subject_id = :subject_id";
            $params['subject_id'] = (int) $filters['subject_id'];
        }

        // Apply search keyword
        if (!empty($filters['search'])) {
            $sql .= " AND (t.title LIKE :search1 OR t.description LIKE :search2 OR s.name LIKE :search3)";
            $searchTerm = '%' . trim($filters['search']) . '%';
            $params['search1'] = $searchTerm;
            $params['search2'] = $searchTerm;
            $params['search3'] = $searchTerm;
        }

        // Apply sorting
        $sort = $filters['sort'] ?? 'due_date';
        $order = (strtoupper($filters['order'] ?? 'ASC') === 'DESC') ? 'DESC' : 'ASC';

        switch ($sort) {
            case 'priority':
                $sql .= " ORDER BY FIELD(t.priority, 'High', 'Medium', 'Low') $order, t.due_date ASC";
                break;
            case 'status':
                $sql .= " ORDER BY t.status $order, t.due_date ASC";
                break;
            case 'title':
                $sql .= " ORDER BY t.title $order";
                break;
            case 'due_date':
            default:
                $sql .= " ORDER BY t.due_date $order, FIELD(t.priority, 'High', 'Medium', 'Low') ASC";
                break;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get single task by ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT t.*, s.name AS subject_name, s.color AS subject_color 
                FROM tasks t 
                JOIN subjects s ON t.subject_id = s.id 
                WHERE t.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $task = $stmt->fetch();
        return $task ?: null;
    }

    /**
     * Create a new task
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO tasks (subject_id, title, description, priority, due_date, status) 
                VALUES (:subject_id, :title, :description, :priority, :due_date, :status)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'subject_id'  => (int) $data['subject_id'],
            'title'       => trim($data['title']),
            'description' => trim($data['description'] ?? ''),
            'priority'    => $data['priority'] ?? 'Medium',
            'due_date'    => $data['due_date'],
            'status'      => $data['status'] ?? 'Pending'
        ]);
    }

    /**
     * Update task
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE tasks 
                SET subject_id = :subject_id,
                    title = :title, 
                    description = :description, 
                    priority = :priority, 
                    due_date = :due_date, 
                    status = :status";
        
        $params = [
            'id'          => $id,
            'subject_id'  => (int) $data['subject_id'],
            'title'       => trim($data['title']),
            'description' => trim($data['description'] ?? ''),
            'priority'    => $data['priority'] ?? 'Medium',
            'due_date'    => $data['due_date'],
            'status'      => $data['status'] ?? 'Pending'
        ];

        if ($data['status'] === 'Completed') {
            $sql .= ", completed_at = COALESCE(completed_at, NOW())";
        } else {
            $sql .= ", completed_at = NULL";
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Toggle task status between Pending and Completed
     */
    public function toggleStatus(int $id): bool {
        $task = $this->getById($id);
        if (!$task) return false;

        $newStatus = ($task['status'] === 'Completed') ? 'Pending' : 'Completed';
        $completedAt = ($newStatus === 'Completed') ? date('Y-m-d H:i:s') : null;

        $stmt = $this->db->prepare(
            "UPDATE tasks SET status = :status, completed_at = :completed_at WHERE id = :id"
        );
        return $stmt->execute([
            'id'           => $id,
            'status'       => $newStatus,
            'completed_at' => $completedAt
        ]);
    }

    /**
     * Delete task
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get summary metrics for Dashboard
     */
    public function getOverallStats(): array {
        $sql = "SELECT 
                    COUNT(*) AS total_tasks,
                    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed_tasks,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_tasks,
                    SUM(CASE WHEN due_date < CURDATE() AND status != 'Completed' THEN 1 ELSE 0 END) AS overdue_tasks,
                    SUM(CASE WHEN due_date = CURDATE() THEN 1 ELSE 0 END) AS today_tasks
                FROM tasks";
        $stmt = $this->db->query($sql);
        $stats = $stmt->fetch();

        $total = (int) ($stats['total_tasks'] ?? 0);
        $completed = (int) ($stats['completed_tasks'] ?? 0);
        
        $stats['total_tasks']     = $total;
        $stats['completed_tasks'] = $completed;
        $stats['pending_tasks']   = (int) ($stats['pending_tasks'] ?? 0);
        $stats['overdue_tasks']   = (int) ($stats['overdue_tasks'] ?? 0);
        $stats['today_tasks']     = (int) ($stats['today_tasks'] ?? 0);
        $stats['completion_rate'] = ($total > 0) ? (int) round(($completed / $total) * 100) : 0;

        return $stats;
    }

    /**
     * Get Today's tasks (or overdue tasks pending action)
     */
    public function getTodayTasks(): array {
        $sql = "SELECT t.*, s.name AS subject_name, s.color AS subject_color,
                       (CASE WHEN t.due_date < CURDATE() THEN 1 ELSE 0 END) AS is_overdue
                FROM tasks t
                JOIN subjects s ON t.subject_id = s.id
                WHERE t.due_date = CURDATE() 
                   OR (t.due_date < CURDATE() AND t.status = 'Pending')
                ORDER BY t.status ASC, FIELD(t.priority, 'High', 'Medium', 'Low') ASC, t.due_date ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get completed task count per day for current week (Mon-Sun)
     */
    public function getWeeklyProductivity(): array {
        $sql = "SELECT 
                    dates.d AS full_date,
                    DATE_FORMAT(dates.d, '%a') AS day_name,
                    COUNT(t.id) AS completed_count
                FROM (
                    SELECT DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) - 0) DAY) AS d
                    UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) - 1) DAY)
                    UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) - 2) DAY)
                    UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) - 3) DAY)
                    UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) - 4) DAY)
                    UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) - 5) DAY)
                    UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) - 6) DAY)
                ) dates
                LEFT JOIN tasks t ON (
                    (DATE(t.completed_at) = dates.d OR (t.due_date = dates.d AND t.status = 'Completed'))
                )
                GROUP BY dates.d
                ORDER BY dates.d ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get Today's study goal target
     */
    public function getTodayGoal(): array {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT * FROM study_goals WHERE goal_date = :today");
        $stmt->execute(['today' => $today]);
        $goal = $stmt->fetch();

        if (!$goal) {
            return [
                'goal_date'    => $today,
                'target_tasks' => 5
            ];
        }
        return $goal;
    }

    /**
     * Set or Update Today's study goal target
     */
    public function setTodayGoal(int $target): bool {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare(
            "INSERT INTO study_goals (goal_date, target_tasks) 
             VALUES (:goal_date, :target) 
             ON DUPLICATE KEY UPDATE target_tasks = :target_update"
        );
        return $stmt->execute([
            'goal_date'     => $today,
            'target'        => max(1, $target),
            'target_update' => max(1, $target)
        ]);
    }

    /**
     * Get count of tasks completed today
     */
    public function getTodayCompletedCount(): int {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM tasks 
             WHERE status = 'Completed' 
               AND (DATE(completed_at) = :today1 OR due_date = :today2)"
        );
        $stmt->execute([
            'today1' => $today,
            'today2' => $today
        ]);
        return (int) $stmt->fetchColumn();
    }
}
