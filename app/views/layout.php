<?php
/**
 * Main Layout Template
 * Provides consistent header, sidebar, topbar, modals, and footer.
 */

$isPublicDir = (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/public/') !== false);
$assetPrefix = $isPublicDir ? 'assets/' : 'public/assets/';
$baseRoute   = $isPublicDir ? 'index.php' : 'index.php';

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'NOVI – Your Personal Study Companion') ?></title>
  <link rel="stylesheet" href="<?= $assetPrefix ?>style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="app-container">
    
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-icon">N</div>
        <div class="brand-info">
          <h1>NOVI</h1>
          <span>Study Companion</span>
        </div>
      </div>

      <nav class="sidebar-nav">
        <a href="<?= $baseRoute ?>?page=dashboard" class="nav-item <?= ($activePage === 'dashboard') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
          Dashboard
        </a>
        <a href="<?= $baseRoute ?>?page=tasks" class="nav-item <?= ($activePage === 'tasks') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
          Tasks
        </a>
        <a href="<?= $baseRoute ?>?page=subjects" class="nav-item <?= ($activePage === 'subjects') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12z"/></svg>
          Subjects
        </a>
      </nav>

      <div class="sidebar-footer">
        NOVI v1.0 &bull; Student Work
      </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
      
      <!-- Topbar Header -->
      <header class="topbar">
        <div class="topbar-left">
          <h2><?= e($pageTitle ?? 'Dashboard') ?></h2>
          <p>Organize, track, and master your learning goals.</p>
        </div>

        <div class="topbar-right">
          <div class="date-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <?= date('l, M j, Y') ?>
          </div>

          <button type="button" class="btn btn-primary btn-sm" id="btnAddTask">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Task
          </button>
        </div>
      </header>

      <!-- Content Container -->
      <main class="content-container">
        
        <?php if ($flash): ?>
          <div class="flash-alert <?= e($flash['type']) ?>">
            <span><?= e($flash['message']) ?></span>
            <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:inherit;font-weight:bold;">&times;</button>
          </div>
        <?php endif; ?>

        <!-- Dynamic Page View Output -->
        <?php require $contentView; ?>

      </main>
    </div>

  </div>

  <!-- Global Modal: Task Create/Edit -->
  <div class="modal-overlay" id="taskModal">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title" id="modalTaskTitle">Create New Task</h3>
        <button type="button" class="modal-close" data-modal-close>&times;</button>
      </div>
      <form action="<?= $baseRoute ?>?page=tasks&action=store" method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" id="taskId" value="">
        
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Task Title *</label>
            <input type="text" name="title" id="taskTitleInput" class="form-control" placeholder="e.g. Complete Python Homework" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Subject *</label>
              <select name="subject_id" id="taskSubjectInput" class="form-control" required>
                <option value="">Select Subject</option>
                <?php 
                $subModel = new Subject();
                foreach ($subModel->getAll() as $sb): 
                ?>
                  <option value="<?= $sb['id'] ?>"><?= e($sb['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Priority</label>
              <select name="priority" id="taskPriorityInput" class="form-control">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Due Date *</label>
              <input type="date" name="due_date" id="taskDueDateInput" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label">Status</label>
              <select name="status" id="taskStatusInput" class="form-control">
                <option value="Pending" selected>Pending</option>
                <option value="Completed">Completed</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Description (Optional)</label>
            <textarea name="description" id="taskDescInput" class="form-control" rows="3" placeholder="Add study notes or sub-tasks..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-primary">Save Task</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Global Modal: Subject Create/Edit -->
  <div class="modal-overlay" id="subjectModal">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title" id="modalSubjectTitle">Create New Subject</h3>
        <button type="button" class="modal-close" data-modal-close>&times;</button>
      </div>
      <form action="<?= $baseRoute ?>?page=subjects&action=store" method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" id="subjectId" value="">
        
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Subject Name *</label>
            <input type="text" name="name" id="subjectNameInput" class="form-control" placeholder="e.g. Machine Learning" required>
          </div>

          <div class="form-group">
            <label class="form-label">Color Accent</label>
            <input type="color" name="color" id="subjectColorInput" class="form-control" value="#4f46e5" style="height:42px;padding:4px;cursor:pointer;">
          </div>

          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" id="subjectDescInput" class="form-control" rows="3" placeholder="Brief subject syllabus or overview..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-primary">Save Subject</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Global Modal: Update Daily Goal -->
  <div class="modal-overlay" id="goalModal">
    <div class="modal-card" style="max-width:380px;">
      <div class="modal-header">
        <h3 class="modal-title">Set Daily Study Goal</h3>
        <button type="button" class="modal-close" data-modal-close>&times;</button>
      </div>
      <form action="<?= $baseRoute ?>?page=dashboard&action=update_goal" method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Target Tasks for Today</label>
            <input type="number" name="target_tasks" class="form-control" min="1" max="50" value="<?= $goal['target_tasks'] ?? 5 ?>" required>
            <span style="font-size:0.8rem;color:var(--text-muted);display:block;margin-top:4px;">How many tasks do you plan to finish today?</span>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-primary">Update Target</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= $assetPrefix ?>script.js"></script>
</body>
</html>
