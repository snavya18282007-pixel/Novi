<?php
/**
 * DashboardController
 * Controls dashboard metrics, daily goal tracking, and productivity visualizations.
 */

require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Subject.php';

class DashboardController {
    private Task $taskModel;
    private Subject $subjectModel;

    public function __construct() {
        $this->taskModel = new Task();
        $this->subjectModel = new Subject();
    }

    /**
     * Show main dashboard
     */
    public function index(): void {
        $stats = $this->taskModel->getOverallStats();
        $totalSubjects = $this->subjectModel->getTotalCount();
        $stats['total_subjects'] = $totalSubjects;

        $todayTasks = $this->taskModel->getTodayTasks();
        $subjects = $this->subjectModel->getAll();

        $goal = $this->taskModel->getTodayGoal();
        $todayCompleted = $this->taskModel->getTodayCompletedCount();

        $weeklyProductivity = $this->taskModel->getWeeklyProductivity();

        // Determine NOVI character companion quote
        $noviMessage = $this->generateNoviMessage($stats, $todayTasks);

        $pageTitle = "Dashboard";
        $activePage = "dashboard";

        // View rendering variables
        $contentView = __DIR__ . '/../views/dashboard.php';
        require __DIR__ . '/../views/layout.php';
    }

    /**
     * Update Daily Study Goal target
     */
    public function updateGoal(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                redirect('index.php?page=dashboard');
            }

            $target = (int) ($_POST['target_tasks'] ?? 5);
            if ($target > 0) {
                $this->taskModel->setTodayGoal($target);
                set_flash('success', 'Daily study goal updated to ' . $target . ' tasks!');
            } else {
                set_flash('error', 'Goal target must be at least 1 task.');
            }
        }
        redirect('index.php?page=dashboard');
    }

    /**
     * Generate dynamic NOVI companion greeting
     */
    private function generateNoviMessage(array $stats, array $todayTasks): array {
        if ($stats['overdue_tasks'] > 0) {
            $count = $stats['overdue_tasks'];
            return [
                'type'    => 'warning',
                'title'   => 'Attention Needed',
                'text'    => "You have {$count} overdue task" . ($count > 1 ? 's' : '') . ". Let's clear them!"
            ];
        }

        $pendingTodayCount = 0;
        foreach ($todayTasks as $t) {
            if ($t['status'] === 'Pending') {
                $pendingTodayCount++;
            }
        }

        if (count($todayTasks) > 0 && $pendingTodayCount === 0) {
            return [
                'type'    => 'success',
                'title'   => 'Awesome Progress!',
                'text'    => "Great work! All of today's tasks are complete."
            ];
        }

        if ($pendingTodayCount > 0) {
            return [
                'type'    => 'info',
                'title'   => 'Focus Time',
                'text'    => "You have {$pendingTodayCount} task" . ($pendingTodayCount > 1 ? 's' : '') . " scheduled for today. Ready to make today productive?"
            ];
        }

        return [
            'type'    => 'info',
            'title'   => 'Welcome Back!',
            'text'    => "Ready to plan your study sessions and boost your learning?"
        ];
    }
}
