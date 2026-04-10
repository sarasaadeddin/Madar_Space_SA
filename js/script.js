const form = document.querySelector(".input-area");
const input = document.getElementById("task-input");
const taskList = document.getElementById("task-list");
const emptyImage = document.querySelector(".empty-image");
const progressContainer = document.querySelector(".progress-container");
const progressFill = document.getElementById("progress-fill");
const progressText = document.getElementById("progress-text");
const progressCircle = document.getElementById("progress-circle");

form.addEventListener("submit", function (e) {
  e.preventDefault(); // يمنع الريفريش

  const taskText = input.value.trim();

  if (taskText === "") return;

  addTask(taskText);
  input.value = "";
});

function addTask(text) {
  const li = document.createElement("li");

  li.innerHTML = `
    <span class="task-text">${text}</span>
    <div class="task-actions">
      <button class="complete-btn"><i class="fa-solid fa-check"></i></button>
      <button class="edit-btn"><i class="fa-solid fa-pen"></i></button>
      <button class="delete-btn"><i class="fa-solid fa-xmark"></i></button>
    </div>
  `;

  taskList.appendChild(li);
  updateProgress();

  const deleteBtn = li.querySelector(".delete-btn");
  const completeBtn = li.querySelector(".complete-btn");
  const editBtn = li.querySelector(".edit-btn");

  deleteBtn.addEventListener("click", () => {
    li.remove();
    updateProgress();
    checkEmpty();
  });

  completeBtn.addEventListener("click", () => {
    li.classList.toggle("completed");
    updateProgress();
  });

  editBtn.addEventListener("click", () => {
    const taskText = li.querySelector(".task-text");
    const newText = prompt("Edit task:", taskText.textContent);
    if (newText !== null && newText.trim() !== "") {
      taskText.textContent = newText;
    }
  });

  checkEmpty();
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