<?php
/**
 * NOVI - Public Entry Point & Simple Router
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/TaskController.php';
require_once __DIR__ . '/../app/controllers/SubjectController.php';

$page   = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

switch ($page) {
    case 'tasks':
        $controller = new TaskController();
        if ($action === 'store') {
            $controller->store();
        } elseif ($action === 'update') {
            $controller->update();
        } elseif ($action === 'toggle') {
            $controller->toggle();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'subjects':
        $controller = new SubjectController();
        if ($action === 'store') {
            $controller->store();
        } elseif ($action === 'update') {
            $controller->update();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'dashboard':
    default:
        $controller = new DashboardController();
        if ($action === 'update_goal') {
            $controller->updateGoal();
        } else {
            $controller->index();
        }
        break;
}
