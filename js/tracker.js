





let currentDate = new Date(selectedDate);

function formatDate(date) {
  return date.toISOString().split('T')[0];
}

function displayDate() {
  document.getElementById("current-date").innerText =
    currentDate.toDateString();
}

function updateProgress() {

  const all = document.querySelectorAll(".habit-checkbox");

  let done = 0;

  all.forEach(c => {
    if(c.checked){
      done++;
    }
  });

  const total = all.length;

  const percent = total
    ? Math.round((done / total) * 100)
    : 0;

  document.getElementById("progress-count").innerText =
    `${done} / ${total} Completed`;

  document.getElementById("progress-percent").innerText =
    percent + "%";

  document.getElementById("progress-fill").style.width =
    percent + "%";
}

displayDate();
updateProgress();


document.getElementById("prev-day").onclick = () => {
  currentDate.setDate(currentDate.getDate() - 1);
   window.location.href = "prayer.php?date=" + formatDate(currentDate);
  // displayDate();
};

document.getElementById("next-day").onclick = () => {
  currentDate.setDate(currentDate.getDate() + 1);
    window.location.href = "prayer.php?date=" + formatDate(currentDate);
  // displayDate();
};



function attachCheckboxEvents(){

  const checkboxes =
    document.querySelectorAll(".habit-checkbox");

  checkboxes.forEach(box => {

    box.addEventListener("change", () => {

      updateProgress();

      const formData = new FormData();

      formData.append(
        "habit_id",
        box.dataset.id
      );

      formData.append(
        "completed",
        box.checked ? 1 : 0
      );

      formData.append(
        "date",
        formatDate(currentDate)
      );

      fetch("php/save_habit.php", {

        method: "POST",

        body: formData

      });

    });

  });

}

attachCheckboxEvents();


const habitModal =
document.getElementById("habitModal");

const addHabitBtn =
document.getElementById("add-habit-btn");

const cancelHabitBtn =
document.getElementById("cancelHabitBtn");

const saveHabitBtn =
document.getElementById("saveHabitBtn");

const habitInput =
document.getElementById("habit-name-input");





addHabitBtn.onclick = () => {

  habitModal.classList.add("active");

  habitInput.focus();
};





cancelHabitBtn.onclick = () => {

  habitModal.classList.remove("active");

  habitInput.value = "";
};





saveHabitBtn.onclick = () => {

  const habitName = habitInput.value.trim();

  if(habitName === ""){
    return;
  }

  const formData = new FormData();

  formData.append(
    "habit_name",
    habitName
  );

  fetch("php/add_habit.php", {

    method: "POST",

    body: formData

  })

  .then(res => res.text())

  .then(id => {

    const customList =
      document.getElementById("custom-list");

    customList.innerHTML += `

      <div class="habit">

        <input
          type="checkbox"
          class="habit-checkbox"
          data-id="${id}"
        >

        <span>${habitName}</span>

      </div>
    `;

    habitModal.classList.remove("active");

    habitInput.value = "";

    updateProgress();

    attachCheckboxEvents();
  });

};

const weeklyModal =
document.getElementById("weeklyModal");

const showWeeklyBtn =
document.getElementById("showWeeklyBtn");

const closeWeeklyBtn =
document.getElementById("closeWeeklyBtn");



showWeeklyBtn.onclick = () => {

  weeklyModal.classList.add("active");

  loadWeeklyProgress();
};



closeWeeklyBtn.onclick = () => {

  weeklyModal.classList.remove("active");
};



function loadWeeklyProgress(){

  const start = new Date(currentDate);

  start.setDate(
    currentDate.getDate() - currentDate.getDay()
  );

  fetch(
    "php/weekly_prog.php?start=" +
    formatDate(start)
  )

  .then(res => res.json())

  .then(data => {

    const container =
      document.getElementById("weekly-progress");

    container.innerHTML = "";

    data.forEach(day => {

      const dayName =
      new Date(day.date)
      .toLocaleDateString(
        "en-US",
        { weekday: "short" }
      );

      container.innerHTML += `

        <div class="week-item">

          <div class="week-day">
            ${dayName}
          </div>

          <div class="week-bar">

            <div
              class="week-fill"
              style="
                width:${day.percent}%
              ">
            </div>

          </div>

          <div class="week-percent">
            ${day.percent}%
          </div>

        </div>
      `;
    });

  });

}
