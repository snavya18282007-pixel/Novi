<?php
/**
 * TaskController
 * Manages task operations including listing, filtering, creation, updating, deletion, and status toggling.
 */

require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Subject.php';

class TaskController {
    private Task $taskModel;
    private Subject $subjectModel;

    public function __construct() {
        $this->taskModel = new Task();
        $this->subjectModel = new Subject();
    }

    /**
     * Display list of tasks with filters, search, and sort options
     */
    public function index(): void {
        $filters = [
            'filter'     => $_GET['filter'] ?? 'all',
            'search'     => $_GET['search'] ?? '',
            'sort'       => $_GET['sort'] ?? 'due_date',
            'order'      => $_GET['order'] ?? 'asc',
            'subject_id' => $_GET['subject_id'] ?? ''
        ];

        $tasks = $this->taskModel->getAll($filters);
        $subjects = $this->subjectModel->getAll();

        $pageTitle = "Task Management";
        $activePage = "tasks";
        $contentView = __DIR__ . '/../views/tasks.php';
        require __DIR__ . '/../views/layout.php';
    }

    /**
     * Store new task
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                redirect('index.php?page=tasks');
            }

            $title       = trim($_POST['title'] ?? '');
            $subjectId   = (int) ($_POST['subject_id'] ?? 0);
            $dueDate     = trim($_POST['due_date'] ?? '');
            $priority    = $_POST['priority'] ?? 'Medium';
            $status      = $_POST['status'] ?? 'Pending';
            $description = trim($_POST['description'] ?? '');

            if (empty($title) || $subjectId <= 0 || empty($dueDate)) {
                set_flash('error', 'Please fill in all required fields (Title, Subject, and Due Date).');
                redirect('index.php?page=tasks');
            }

            $success = $this->taskModel->create([
                'title'       => $title,
                'subject_id'  => $subjectId,
                'due_date'    => $dueDate,
                'priority'    => $priority,
                'status'      => $status,
                'description' => $description
            ]);

            if ($success) {
                set_flash('success', 'Task "' . e($title) . '" created successfully!');
            } else {
                set_flash('error', 'Failed to create task. Please try again.');
            }
        }
        redirect('index.php?page=tasks');
    }

    /**
     * Update existing task
     */
    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                redirect('index.php?page=tasks');
            }

            $id          = (int) ($_POST['id'] ?? 0);
            $title       = trim($_POST['title'] ?? '');
            $subjectId   = (int) ($_POST['subject_id'] ?? 0);
            $dueDate     = trim($_POST['due_date'] ?? '');
            $priority    = $_POST['priority'] ?? 'Medium';
            $status      = $_POST['status'] ?? 'Pending';
            $description = trim($_POST['description'] ?? '');

            if ($id <= 0 || empty($title) || $subjectId <= 0 || empty($dueDate)) {
                set_flash('error', 'Invalid input parameters for editing task.');
                redirect('index.php?page=tasks');
            }

            $success = $this->taskModel->update($id, [
                'title'       => $title,
                'subject_id'  => $subjectId,
                'due_date'    => $dueDate,
                'priority'    => $priority,
                'status'      => $status,
                'description' => $description
            ]);

            if ($success) {
                set_flash('success', 'Task "' . e($title) . '" updated successfully!');
            } else {
                set_flash('error', 'Failed to update task.');
            }
        }
        redirect('index.php?page=tasks');
    }

    /**
     * Toggle task status between Pending and Completed
     */
    public function toggle(): void {
        $id = (int) ($_REQUEST['id'] ?? 0);
        if ($id > 0) {
            $task = $this->taskModel->getById($id);
            if ($task) {
                $this->taskModel->toggleStatus($id);
                $newStatus = ($task['status'] === 'Completed') ? 'Pending' : 'Completed';
                set_flash('success', 'Task marked as ' . strtolower($newStatus) . '!');
            }
        }

        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? 'index.php?page=tasks';
        redirect($redirectUrl);
    }

    /**
     * Delete task
     */
    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                redirect('index.php?page=tasks');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $task = $this->taskModel->getById($id);
                if ($task && $this->taskModel->delete($id)) {
                    set_flash('success', 'Task deleted successfully!');
                } else {
                    set_flash('error', 'Could not delete task.');
                }
            }
        }
        redirect('index.php?page=tasks');
    }
}
