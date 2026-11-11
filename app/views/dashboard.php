<?php
/**
 * Dashboard View
 * Renders NOVI banner, statistics cards, today's task list, study goal tracker,
 * subject progress bars, and weekly productivity chart.
 */
?>

<!-- NOVI Character Banner -->
<div class="novi-banner">
  <div class="novi-banner-left">
    <div class="novi-avatar">
      <?php if (($stats['overdue_tasks'] ?? 0) > 0): ?>
        ⚠️
      <?php else: ?>
        ⚡
      <?php endif; ?>
    </div>
    <div class="novi-banner-text">
      <h3>NOVI Companion &bull; <?= e($noviMessage['title']) ?></h3>
      <p>"<?= e($noviMessage['text']) ?>"</p>
    </div>
  </div>
  <button type="button" class="btn btn-secondary btn-sm" id="btnAddTask">
    + Quick Add Task
  </button>
</div>

<!-- Key Metrics Grid -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon primary">📋</div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['total_tasks'] ?></div>
      <div class="stat-label">Total Tasks</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon success">✅</div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['completed_tasks'] ?></div>
      <div class="stat-label">Completed</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon warning">⏳</div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['pending_tasks'] ?></div>
      <div class="stat-label">Pending</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon danger">🚨</div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['overdue_tasks'] ?></div>
      <div class="stat-label">Overdue Tasks</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon purple">📚</div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['total_subjects'] ?></div>
      <div class="stat-label">Total Subjects</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon primary">🎯</div>
    <div class="stat-info">
      <div class="stat-value"><?= $stats['completion_rate'] ?>%</div>
      <div class="stat-label">Overall Completion</div>
    </div>
  </div>
</div>

<!-- Main Dashboard Grid -->
<div class="dashboard-grid">
  
  <!-- Left Column: Today's Tasks & Subject Progress -->
  <div class="dashboard-left">
    
    <!-- Today's Tasks Section -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
          Today's Tasks & Focus List
        </div>
        <a href="index.php?page=tasks&filter=today" style="font-size:0.85rem;color:var(--primary);text-decoration:none;font-weight:600;">View All &rarr;</a>
      </div>

      <div class="task-list">
        <?php if (empty($todayTasks)): ?>
          <div class="empty-state">
            <div class="empty-icon">🎉</div>
            <div class="empty-title">All Clear for Today!</div>
            <p>You have no pending tasks scheduled for today. Great time to relax or add a new goal.</p>
          </div>
        <?php else: ?>
          <?php foreach ($todayTasks as $task): ?>
            <div class="task-item <?= ($task['status'] === 'Completed') ? 'completed' : '' ?> <?= (!empty($task['is_overdue']) && $task['status'] !== 'Completed') ? 'overdue' : '' ?>">
              <div class="task-left">
                <input type="checkbox" class="task-checkbox" data-task-id="<?= $task['id'] ?>" <?= ($task['status'] === 'Completed') ? 'checked' : '' ?>>
                <div class="task-content">
                  <div class="task-title"><?= e($task['title']) ?></div>
                  <div class="task-meta">
                    <span class="subject-badge">
                      <span class="color-dot" style="background-color: <?= e($task['subject_color']) ?>"></span>
                      <?= e($task['subject_name']) ?>
                    </span>
                    <span class="priority-badge priority-<?= strtolower($task['priority']) ?>">
                      <?= e($task['priority']) ?>
                    </span>
                    <span>Due: <?= date('M j', strtotime($task['due_date'])) ?></span>

                    <?php if (!empty($task['is_overdue']) && $task['status'] !== 'Completed'): ?>
                      <span class="overdue-tag">🚨 OVERDUE</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <div class="task-right">
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
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Subject Progress Section -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
          Subject-Wise Progress
        </div>
        <a href="index.php?page=subjects" style="font-size:0.85rem;color:var(--primary);text-decoration:none;font-weight:600;">Manage Subjects &rarr;</a>
      </div>

      <div style="display:flex;flex-direction:column;gap:1.25rem;">
        <?php if (empty($subjects)): ?>
          <div class="empty-state">
            <p>No subjects created yet. Add a subject to categorize your study tasks.</p>
          </div>
        <?php else: ?>
          <?php foreach ($subjects as $sb): ?>
            <div class="progress-container">
              <div class="progress-label">
                <span style="display:flex;align-items:center;gap:0.4rem;">
                  <span class="color-dot" style="background-color: <?= e($sb['color']) ?>;width:10px;height:10px;"></span>
                  <strong><?= e($sb['name']) ?></strong>
                </span>
                <span><?= $sb['completed_tasks'] ?> / <?= $sb['total_tasks'] ?> Tasks (<?= $sb['completion_percentage'] ?>%)</span>
              </div>
              <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: <?= $sb['completion_percentage'] ?>%; background-color: <?= e($sb['color']) ?>;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <!-- Right Column: Daily Goal & Weekly Productivity -->
  <div class="dashboard-right">
    
    <!-- Daily Study Goal Widget -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          🎯 Today's Goal
        </div>
        <button type="button" class="btn btn-secondary btn-sm" data-modal-open="goalModal">Edit Target</button>
      </div>

      <?php 
      $target = (int) ($goal['target_tasks'] ?? 5);
      $goalPercent = ($target > 0) ? min(100, round(($todayCompleted / $target) * 100)) : 0;
      ?>

      <div style="text-align:center;padding:0.5rem 0;">
        <div style="font-size:2.2rem;font-weight:700;color:var(--primary);line-height:1;">
          <?= $todayCompleted ?> <span style="font-size:1.2rem;color:var(--text-muted);font-weight:400;">/ <?= $target ?></span>
        </div>
        <div style="font-size:0.85rem;color:var(--text-muted);margin-top:0.25rem;margin-bottom:1.25rem;">Tasks Completed Today</div>

        <div class="progress-bar-bg" style="height:14px;">
          <div class="progress-bar-fill" style="width: <?= $goalPercent ?>%;"></div>
        </div>

        <div style="margin-top:0.75rem;font-size:0.9rem;font-weight:600;color:var(--text-medium);">
          <?= $goalPercent ?>% Goal Reached
        </div>
      </div>
    </div>

    <!-- Weekly Productivity CSS Chart -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          📊 Productivity This Week
        </div>
      </div>

      <?php
      $maxTasks = 1;
      foreach ($weeklyProductivity as $wp) {
          if ($wp['completed_count'] > $maxTasks) {
              $maxTasks = $wp['completed_count'];
          }
      }
      ?>

      <div class="chart-container">
        <?php foreach ($weeklyProductivity as $day): ?>
          <?php 
          $height = ($maxTasks > 0) ? round(($day['completed_count'] / $maxTasks) * 100) : 0;
          $height = max(10, $height); // minimum visible bar height
          $isToday = (date('Y-m-d') === $day['full_date']);
          ?>
          <div class="chart-bar-group">
            <div class="chart-bar-val"><?= $day['completed_count'] ?></div>
            <div class="chart-bar-wrapper">
              <div class="chart-bar-fill" style="height: <?= $height ?>%; <?= $isToday ? 'background: linear-gradient(180deg, #10b981 0%, #059669 100%);' : '' ?>"></div>
            </div>
            <div class="chart-day-label" style="<?= $isToday ? 'color:var(--primary);font-weight:700;' : '' ?>"><?= e($day['day_name']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

</div>
