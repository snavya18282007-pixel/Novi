<?php
/**
 * SubjectController
 * Manages subject operations including listing, adding, updating, and deleting subjects.
 */

require_once __DIR__ . '/../models/Subject.php';

class SubjectController {
    private Subject $subjectModel;

    public function __construct() {
        $this->subjectModel = new Subject();
    }

    /**
     * Display subjects overview
     */
    public function index(): void {
        $subjects = $this->subjectModel->getAll();

        $pageTitle = "Subject Management";
        $activePage = "subjects";
        $contentView = __DIR__ . '/../views/subjects.php';
        require __DIR__ . '/../views/layout.php';
    }

    /**
     * Store new subject
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                redirect('index.php?page=subjects');
            }

            $name        = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $color       = trim($_POST['color'] ?? '#4f46e5');

            if (empty($name)) {
                set_flash('error', 'Subject name is required.');
                redirect('index.php?page=subjects');
            }

            $success = $this->subjectModel->create([
                'name'        => $name,
                'description' => $description,
                'color'       => $color
            ]);

            if ($success) {
                set_flash('success', 'Subject "' . e($name) . '" created successfully!');
            } else {
                set_flash('error', 'Failed to create subject.');
            }
        }
        redirect('index.php?page=subjects');
    }

    /**
     * Update existing subject
     */
    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                redirect('index.php?page=subjects');
            }

            $id          = (int) ($_POST['id'] ?? 0);
            $name        = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $color       = trim($_POST['color'] ?? '#4f46e5');

            if ($id <= 0 || empty($name)) {
                set_flash('error', 'Invalid input parameters for editing subject.');
                redirect('index.php?page=subjects');
            }

            $success = $this->subjectModel->update($id, [
                'name'        => $name,
                'description' => $description,
                'color'       => $color
            ]);

            if ($success) {
                set_flash('success', 'Subject "' . e($name) . '" updated successfully!');
            } else {
                set_flash('error', 'Failed to update subject.');
            }
        }
        redirect('index.php?page=subjects');
    }

    /**
     * Delete subject
     */
    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                redirect('index.php?page=subjects');
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $subject = $this->subjectModel->getById($id);
                if ($subject && $this->subjectModel->delete($id)) {
                    set_flash('success', 'Subject deleted successfully!');
                } else {
                    set_flash('error', 'Could not delete subject.');
                }
            }
        }
        redirect('index.php?page=subjects');
    }
}
