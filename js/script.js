const API_BASE = "/Madar_Space/Madar_Space_SA/php";

const form = document.querySelector(".input-area");
const input = document.getElementById("task-input");
const taskList = document.getElementById("task-list");
const emptyImage = document.querySelector(".empty-image");
const progressContainer = document.querySelector(".progress-container");
const progressFill = document.getElementById("progress-fill");
const progressText = document.getElementById("progress-text");
const progressCircle = document.getElementById("progress-circle");
const todayDate = document.getElementById("today-date");

let tasks = [];

if (todayDate) {
  const today = new Date();

  todayDate.textContent = today.toLocaleDateString("en-US", {
    weekday: "long",
    month: "long",
    day: "numeric",
    year: "numeric"
  });
}

loadTasks();

form.addEventListener("submit", function (e) {
  e.preventDefault();

  const taskText = input.value.trim();

  if (taskText === "") return;

  addTaskToDatabase(taskText);
  input.value = "";
});

function loadTasks() {
  fetch(`${API_BASE}/get_todos.php`)
    .then(response => response.json())
    .then(data => {
      tasks = data;
      renderTasks();
    })
    .catch(error => {
      console.error("Error loading tasks:", error);
    });
}

function renderTasks() {
  taskList.innerHTML = "";

  tasks.forEach(task => {
    const li = document.createElement("li");

    if (Number(task.completed) === 1) {
      li.classList.add("completed");
    }

    li.innerHTML = `
      <span class="task-text">${task.task}</span>
      <div class="task-actions">
        <button class="complete-btn" data-id="${task.id}" data-completed="${task.completed}">
          <i class="fa-solid fa-check"></i>
        </button>
        <button class="edit-btn" data-id="${task.id}">
          <i class="fa-solid fa-pen"></i>
        </button>
        <button class="delete-btn" data-id="${task.id}">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    `;

    taskList.appendChild(li);
  });

  attachTaskEvents();
  updateProgress();
  checkEmpty();
}

function attachTaskEvents() {
  document.querySelectorAll(".delete-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      deleteTaskFromDatabase(id);
    });
  });

  document.querySelectorAll(".complete-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      const currentCompleted = Number(this.getAttribute("data-completed"));
      const newCompleted = currentCompleted === 1 ? 0 : 1;

      toggleTaskInDatabase(id, newCompleted);
    });
  });

  document.querySelectorAll(".edit-btn").forEach(btn => {
    btn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      const li = this.closest("li");
      const taskText = li.querySelector(".task-text");

      const newText = prompt("Edit task:", taskText.textContent);

      if (newText !== null && newText.trim() !== "") {
        updateTaskInDatabase(id, newText.trim());
      }
    });
  });
}

function addTaskToDatabase(taskText) {
  const formData = new FormData();
  formData.append("task", taskText);

  fetch(`${API_BASE}/add_todo.php`, {
    method: "POST",
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        loadTasks();
      } else {
        alert(data.message || "Failed to add task");
      }
    })
    .catch(error => {
      console.error("Error adding task:", error);
    });
}

function deleteTaskFromDatabase(id) {
  const formData = new FormData();
  formData.append("id", id);

  fetch(`${API_BASE}/delete_todo.php`, {
    method: "POST",
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        loadTasks();
      } else {
        alert(data.message || "Failed to delete task");
      }
    })
    .catch(error => {
      console.error("Error deleting task:", error);
    });
}

function toggleTaskInDatabase(id, completed) {
  const formData = new FormData();
  formData.append("id", id);
  formData.append("completed", completed);

  fetch(`${API_BASE}/toggle_todo.php`, {
    method: "POST",
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        loadTasks();
      } else {
        alert(data.message || "Failed to update task");
      }
    })
    .catch(error => {
      console.error("Error updating task:", error);
    });
}

function updateTaskInDatabase(id, taskText) {
  const formData = new FormData();
  formData.append("id", id);
  formData.append("task", taskText);

  fetch(`${API_BASE}/update_todo.php`, {
    method: "POST",
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        loadTasks();
      } else {
        alert(data.message || "Failed to edit task");
      }
    })
    .catch(error => {
      console.error("Error editing task:", error);
    });
}

function checkEmpty() {
  if (taskList.children.length === 0) {
    emptyImage.style.display = "block";
  } else {
    emptyImage.style.display = "none";
  }
}

function updateProgress() {
  const totalTasks = taskList.children.length;

  if (totalTasks === 0) {
    progressContainer.style.display = "none";
    return;
  }

  progressContainer.style.display = "block";

  const completedTasks = document.querySelectorAll(".completed").length;
  const percentage = Math.round((completedTasks / totalTasks) * 100);

  progressFill.style.width = percentage + "%";
  progressText.textContent = `${completedTasks} / ${totalTasks} Completed`;
  progressCircle.textContent = percentage + "%";
}