<?php
/**
 * Subjects View
 * Displays subject cards grid with total/completed tasks metrics, completion percentage,
 * and CRUD modal actions.
 */
?>

<div class="card" style="margin-bottom:1.5rem;">
  <div class="card-header" style="margin-bottom:0;padding-bottom:0;border-bottom:none;">
    <div class="card-title">
      📚 Subject Overview (<?= count($subjects) ?>)
    </div>
    <button type="button" class="btn btn-primary btn-sm" id="btnAddSubject">+ Add Subject</button>
  </div>
</div>

<!-- Subjects Cards Grid -->
<div class="subjects-grid">
  <?php if (empty($subjects)): ?>
    <div class="card" style="grid-column: 1 / -1;">
      <div class="empty-state">
        <div class="empty-icon">📖</div>
        <div class="empty-title">No Subjects Created Yet</div>
        <p>Click "Add Subject" above to organize your study topics.</p>
      </div>
    </div>
  <?php else: ?>
    <?php foreach ($subjects as $subject): ?>
      <div class="subject-card" style="--subject-accent: <?= e($subject['color']) ?>;">
        
        <div>
          <div class="subject-card-header">
            <h3 class="subject-card-title"><?= e($subject['name']) ?></h3>
            <span class="color-dot" style="background-color: <?= e($subject['color']) ?>; width:14px; height:14px;"></span>
          </div>

          <p class="subject-card-desc">
            <?= e($subject['description'] ?: 'No description provided.') ?>
          </p>

          <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:0.75rem;display:flex;justify-content:space-between;">
            <span>Tasks: <strong><?= $subject['total_tasks'] ?></strong></span>
            <span>Completed: <strong><?= $subject['completed_tasks'] ?></strong></span>
          </div>

          <!-- Progress Bar -->
          <div class="progress-container" style="margin-bottom:1.5rem;">
            <div class="progress-label">
              <span>Completion Rate</span>
              <span><?= $subject['completion_percentage'] ?>%</span>
            </div>
            <div class="progress-bar-bg">
              <div class="progress-bar-fill" style="width: <?= $subject['completion_percentage'] ?>%; background-color: <?= e($subject['color']) ?>;"></div>
            </div>
          </div>
        </div>

        <!-- Card Footer Action Bar -->
        <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid #f1f5f9;padding-top:1rem;margin-top:0.5rem;">
          <a href="index.php?page=tasks&subject_id=<?= $subject['id'] ?>" class="btn btn-secondary btn-sm">
            View Tasks &rarr;
          </a>

          <div style="display:flex;gap:0.3rem;">
            <!-- Edit Button -->
            <button type="button" class="btn-icon btn-edit-subject" 
                    data-id="<?= $subject['id'] ?>"
                    data-name="<?= e($subject['name']) ?>"
                    data-description="<?= e($subject['description']) ?>"
                    data-color="<?= e($subject['color']) ?>"
                    title="Edit Subject">
              ✏️
            </button>

            <!-- Delete Form -->
            <form action="index.php?page=subjects&action=delete" method="POST" onsubmit="return confirm('Deleting this subject will also delete all associated tasks. Are you sure?');">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
              <input type="hidden" name="id" value="<?= $subject['id'] ?>">
              <button type="submit" class="btn-icon" style="color:#ef4444;" title="Delete Subject">🗑️</button>
            </form>
          </div>
        </div>

      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
