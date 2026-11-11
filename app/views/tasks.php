<?php
/**
 * Tasks View
 * Manages tasks with search, tab filters, sorting, status toggles, and modal CRUD.
 */
?>

<!-- Filter & Search Toolbar -->
<div class="toolbar">
  
  <!-- Filter Tabs -->
  <div class="filter-tabs">
    <a href="index.php?page=tasks&filter=all&search=<?= urlencode($filters['search']) ?>&sort=<?= $filters['sort'] ?>&order=<?= $filters['order'] ?>" 
       class="tab-item <?= ($filters['filter'] === 'all') ? 'active' : '' ?>">
       All Tasks
    </a>
    <a href="index.php?page=tasks&filter=today&search=<?= urlencode($filters['search']) ?>&sort=<?= $filters['sort'] ?>&order=<?= $filters['order'] ?>" 
       class="tab-item <?= ($filters['filter'] === 'today') ? 'active' : '' ?>">
       Today
    </a>
    <a href="index.php?page=tasks&filter=upcoming&search=<?= urlencode($filters['search']) ?>&sort=<?= $filters['sort'] ?>&order=<?= $filters['order'] ?>" 
       class="tab-item <?= ($filters['filter'] === 'upcoming') ? 'active' : '' ?>">
       Upcoming
    </a>
    <a href="index.php?page=tasks&filter=pending&search=<?= urlencode($filters['search']) ?>&sort=<?= $filters['sort'] ?>&order=<?= $filters['order'] ?>" 
       class="tab-item <?= ($filters['filter'] === 'pending') ? 'active' : '' ?>">
       Pending
    </a>
    <a href="index.php?page=tasks&filter=overdue&search=<?= urlencode($filters['search']) ?>&sort=<?= $filters['sort'] ?>&order=<?= $filters['order'] ?>" 
       class="tab-item <?= ($filters['filter'] === 'overdue') ? 'active' : '' ?>">
       🚨 Overdue
    </a>
    <a href="index.php?page=tasks&filter=completed&search=<?= urlencode($filters['search']) ?>&sort=<?= $filters['sort'] ?>&order=<?= $filters['order'] ?>" 
       class="tab-item <?= ($filters['filter'] === 'completed') ? 'active' : '' ?>">
       Completed
    </a>
  </div>

  <!-- Search & Sorting Controls -->
  <form action="index.php" method="GET" class="toolbar-search-sort">
    <input type="hidden" name="page" value="tasks">
    <input type="hidden" name="filter" value="<?= e($filters['filter']) ?>">

    <!-- Search Input -->
    <div class="search-box">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" placeholder="Search tasks..." value="<?= e($filters['search']) ?>" onchange="this.form.submit()">
    </div>

    <!-- Subject Dropdown Filter -->
    <select name="subject_id" class="select-control" onchange="this.form.submit()">
      <option value="">All Subjects</option>
      <?php foreach ($subjects as $sb): ?>
        <option value="<?= $sb['id'] ?>" <?= ($filters['subject_id'] == $sb['id']) ? 'selected' : '' ?>>
          <?= e($sb['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <!-- Sort Dropdown -->
    <select name="sort" class="select-control" onchange="this.form.submit()">
      <option value="due_date" <?= ($filters['sort'] === 'due_date') ? 'selected' : '' ?>>Sort by Due Date</option>
      <option value="priority" <?= ($filters['sort'] === 'priority') ? 'selected' : '' ?>>Sort by Priority</option>
      <option value="status" <?= ($filters['sort'] === 'status') ? 'selected' : '' ?>>Sort by Status</option>
      <option value="title" <?= ($filters['sort'] === 'title') ? 'selected' : '' ?>>Sort by Title</option>
    </select>

    <!-- Order Toggle -->
    <select name="order" class="select-control" onchange="this.form.submit()">
      <option value="asc" <?= ($filters['order'] === 'asc') ? 'selected' : '' ?>>Ascending</option>
      <option value="desc" <?= ($filters['order'] === 'desc') ? 'selected' : '' ?>>Descending</option>
    </select>

    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
  </form>

</div>

<!-- Task List Section -->
<div class="card">
  <div class="card-header">
    <div class="card-title">
      Tasks List (<?= count($tasks) ?>)
    </div>
    <button type="button" class="btn btn-primary btn-sm" id="btnAddTask">+ Add Task</button>
  </div>

  <div class="task-list">
    <?php if (empty($tasks)): ?>
      <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <div class="empty-title">No tasks found</div>
        <p>Try adjusting your search criteria, filter tab, or create a new task.</p>
      </div>
    <?php else: ?>
      <?php foreach ($tasks as $task): ?>
        <div class="task-item <?= ($task['status'] === 'Completed') ? 'completed' : '' ?> <?= (!empty($task['is_overdue']) && $task['status'] !== 'Completed') ? 'overdue' : '' ?>">
          
          <div class="task-left">
            <input type="checkbox" class="task-checkbox" data-task-id="<?= $task['id'] ?>" <?= ($task['status'] === 'Completed') ? 'checked' : '' ?>>
            
            <div class="task-content">
              <div class="task-title"><?= e($task['title']) ?></div>
              
              <?php if (!empty($task['description'])): ?>
                <div style="font-size:0.82rem;color:var(--text-muted);margin-top:2px;margin-bottom:2px;">
                  <?= e($task['description']) ?>
                </div>
              <?php endif; ?>

              <div class="task-meta">
                <span class="subject-badge">
                  <span class="color-dot" style="background-color: <?= e($task['subject_color']) ?>"></span>
                  <?= e($task['subject_name']) ?>
                </span>
                
                <span class="priority-badge priority-<?= strtolower($task['priority']) ?>">
                  <?= e($task['priority']) ?>
                </span>

                <span>Due: <?= date('M j, Y', strtotime($task['due_date'])) ?></span>

                <?php if (!empty($task['is_overdue']) && $task['status'] !== 'Completed'): ?>
                  <span class="overdue-tag">🚨 OVERDUE</span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="task-right" style="display:flex;align-items:center;gap:0.5rem;">
            <!-- Status Toggle Link -->
            <a href="index.php?page=tasks&action=toggle&id=<?= $task['id'] ?>" 
               class="btn btn-secondary btn-sm" style="font-size:0.75rem;">
               <?= ($task['status'] === 'Completed') ? 'Reopen' : 'Complete' ?>
            </a>

            <!-- Edit Button -->
            <button type="button" class="btn-icon btn-edit-task" 
                    data-id="<?= $task['id'] ?>"
                    data-title="<?= e($task['title']) ?>"
                    data-description="<?= e($task['description']) ?>"
                    data-subject-id="<?= $task['subject_id'] ?>"
                    data-priority="<?= $task['priority'] ?>"
                    data-due-date="<?= $task['due_date'] ?>"
                    data-status="<?= $task['status'] ?>"
                    title="Edit Task">
              ✏️
            </button>

            <!-- Delete Form -->
            <form action="index.php?page=tasks&action=delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
              <input type="hidden" name="id" value="<?= $task['id'] ?>">
              <button type="submit" class="btn-icon" style="color:#ef4444;" title="Delete Task">🗑️</button>
            </form>
          </div>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
