

const prayers = [" Fajr", " Dhuhr", " Asr", " Maghrib", " Isha"];
const athkar = [" Duha Prayer", " Morning Athkar", " Evening Athkar", " Tahajjud"];

let customHabits = [];


let data = {};

let currentDate = new Date();




function formatDate(date) {
  return date.toISOString().split('T')[0];
}

function displayDate() {
  document.getElementById("current-date").innerText =
    currentDate.toDateString();
}




function loadPage() {
  displayDate();

  document.getElementById("prayers-list").innerHTML = "";
  document.getElementById("athkar-list").innerHTML = "";
  document.getElementById("custom-list").innerHTML = "";

  prayers.forEach(h => createHabit(h, "prayers-list"));
  athkar.forEach(h => createHabit(h, "athkar-list"));
  customHabits.forEach(h => createHabit(h, "custom-list"));

  updateProgress();
}



function createHabit(name, containerId) {
  const container = document.getElementById(containerId);
  const div = document.createElement("div");
  div.className = "habit";

  const checkbox = document.createElement("input");
  checkbox.type = "checkbox";

  const key = formatDate(currentDate) + "-" + name;

 
  checkbox.checked = data[key] || false;

  checkbox.addEventListener("change", () => {
    data[key] = checkbox.checked;   // 🔥 حفظ الحالة
    updateProgress();
  });

  const label = document.createElement("span");
  label.innerText = name;

  div.appendChild(checkbox);
  div.appendChild(label);

  container.appendChild(div);
}







// function updateProgress() {
//   const all  = document.querySelectorAll("input[type='checkbox']");
//   let done   = 0;

//   all.forEach(c => { if (c.checked) done++; });

//   const total   = all.length;
//   const percent = total ? Math.round((done / total) * 100) : 0;

//   document.getElementById("progress-text").innerText    = percent + "%";
//   document.getElementById("progress-fill").style.width  = percent + "%";
// }

function updateProgress() {
  const all = document.querySelectorAll("input[type='checkbox']");
  let done = 0;

  all.forEach(c => {
    if (c.checked) done++;
  });

  const total = all.length;
  const percent = total ? Math.round((done / total) * 100) : 0;

  
  document.getElementById("progress-count").innerText =
    done + " / " + total + " Completed";

  document.getElementById("progress-percent").innerText =
    percent + "%";

  
  document.getElementById("progress-fill").style.width =
    percent + "%";
}





document.getElementById("add-habit-btn").addEventListener("click", () => {
  const name = prompt("Enter habit name:");
  if (name) {
    customHabits.push(name);
    loadPage();
  }
});




document.getElementById("prev-day").onclick = () => {
  currentDate.setDate(currentDate.getDate() - 1);
  loadPage();
};

document.getElementById("next-day").onclick = () => {
  currentDate.setDate(currentDate.getDate() + 1);
  loadPage();
};





loadPage();