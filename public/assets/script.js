/**
 * NOVI - Personal Study Companion Frontend Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Auto-dismiss Flash Alerts
  const flashAlerts = document.querySelectorAll('.flash-alert');
  flashAlerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transition = 'opacity 0.5s ease';
      setTimeout(() => alert.remove(), 500);
    }, 4000);
  });

  // 2. Generic Modal Open/Close Controls
  document.querySelectorAll('[data-modal-open]').forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      const targetId = button.getAttribute('data-modal-open');
      openModal(targetId);
    });
  });

  document.querySelectorAll('[data-modal-close]').forEach(button => {
    button.addEventListener('click', () => {
      const modal = button.closest('.modal-overlay');
      if (modal) closeModal(modal.id);
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeModal(modal.id);
      }
    });
  });

  // 3. Edit Task Modal Setup
  document.querySelectorAll('.btn-edit-task').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const title = button.getAttribute('data-title');
      const description = button.getAttribute('data-description');
      const subjectId = button.getAttribute('data-subject-id');
      const priority = button.getAttribute('data-priority');
      const dueDate = button.getAttribute('data-due-date');
      const status = button.getAttribute('data-status');

      const modal = document.getElementById('taskModal');
      if (modal) {
        modal.querySelector('#modalTaskTitle').textContent = 'Edit Task';
        modal.querySelector('form').action = 'index.php?page=tasks&action=update';
        modal.querySelector('#taskId').value = id;
        modal.querySelector('#taskTitleInput').value = title;
        modal.querySelector('#taskDescInput').value = description;
        modal.querySelector('#taskSubjectInput').value = subjectId;
        modal.querySelector('#taskPriorityInput').value = priority;
        modal.querySelector('#taskDueDateInput').value = dueDate;
        modal.querySelector('#taskStatusInput').value = status;
        openModal('taskModal');
      }
    });
  });

  // 4. Edit Subject Modal Setup
  document.querySelectorAll('.btn-edit-subject').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const name = button.getAttribute('data-name');
      const description = button.getAttribute('data-description');
      const color = button.getAttribute('data-color');

      const modal = document.getElementById('subjectModal');
      if (modal) {
        modal.querySelector('#modalSubjectTitle').textContent = 'Edit Subject';
        modal.querySelector('form').action = 'index.php?page=subjects&action=update';
        modal.querySelector('#subjectId').value = id;
        modal.querySelector('#subjectNameInput').value = name;
        modal.querySelector('#subjectDescInput').value = description;
        modal.querySelector('#subjectColorInput').value = color;
        openModal('subjectModal');
      }
    });
  });

  // 5. Reset Task Modal on Add New Click
  const btnAddTask = document.getElementById('btnAddTask');
  if (btnAddTask) {
    btnAddTask.addEventListener('click', () => {
      const modal = document.getElementById('taskModal');
      if (modal) {
        modal.querySelector('#modalTaskTitle').textContent = 'Create New Task';
        modal.querySelector('form').action = 'index.php?page=tasks&action=store';
        modal.querySelector('#taskId').value = '';
        modal.querySelector('form').reset();
        
        // Default due date to today
        const today = new Date().toISOString().split('T')[0];
        const dueDateInput = modal.querySelector('#taskDueDateInput');
        if (dueDateInput && !dueDateInput.value) {
          dueDateInput.value = today;
        }
        openModal('taskModal');
      }
    });
  }

  // 6. Reset Subject Modal on Add New Click
  const btnAddSubject = document.getElementById('btnAddSubject');
  if (btnAddSubject) {
    btnAddSubject.addEventListener('click', () => {
      const modal = document.getElementById('subjectModal');
      if (modal) {
        modal.querySelector('#modalSubjectTitle').textContent = 'Create New Subject';
        modal.querySelector('form').action = 'index.php?page=subjects&action=store';
        modal.querySelector('#subjectId').value = '';
        modal.querySelector('form').reset();
        openModal('subjectModal');
      }
    });
  }

  // 7. Instant Form Submit on Task Checkbox Toggle
  document.querySelectorAll('.task-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', (e) => {
      const taskId = checkbox.getAttribute('data-task-id');
      if (taskId) {
        window.location.href = `index.php?page=tasks&action=toggle&id=${taskId}`;
      }
    });
  });
});

// Helper Modal Functions
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}
