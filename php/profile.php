<?php

session_start();

include("connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$selectedWeek=0;

if(isset($_GET['week'])){
$selectedWeek=$_GET['week'];
}
$userQuery = mysqli_query($conn,
    "SELECT * FROM users
     WHERE id='$user_id'"
);

$user = mysqli_fetch_assoc($userQuery);

$habitQuery = mysqli_query($conn,
    "SELECT COUNT(*) as total
     FROM habit_logs
     WHERE user_id='$user_id'
     AND completed=1"
);
$habitData = mysqli_fetch_assoc($habitQuery);
$totalCompletedHabits =
$habitData['total'];

//for task 

$completedTasksQuery = mysqli_query($conn,
   "SELECT COUNT(*) as total
    FROM todo
    WHERE user_id='$user_id'
    AND completed=1"
);

   $completedTasksData = mysqli_fetch_assoc($completedTasksQuery);

   $totalCompletedTasks = $completedTasksData['total'];



   $totalTasksQuery = mysqli_query($conn,
   "SELECT COUNT(*) as total
   FROM todo
   WHERE user_id='$user_id'"
   );

$totalTasksData = mysqli_fetch_assoc($totalTasksQuery);

$totalTasks = $totalTasksData['total'];





$weekData = [];

for($i = 6; $i >= 0; $i--){

    $date = date(
        "Y-m-d",
       strtotime("-".($i + ($selectedWeek*7))." days")
    );

    $totalQuery = mysqli_query($conn,
        "SELECT COUNT(*) as total
         FROM habits
         WHERE user_id='$user_id'"
    );

    $totalHabits =
    mysqli_fetch_assoc($totalQuery)['total'];
    $doneQuery = mysqli_query($conn,
        "SELECT COUNT(*) as done
         FROM habit_logs
         WHERE user_id='$user_id'
         AND completed=1
         AND completed_date='$date'"
    );
    
    $doneHabits =
    mysqli_fetch_assoc($doneQuery)['done'];
    $percent = 0;

    if($totalHabits > 0){

        $percent = round(
            ($doneHabits / $totalHabits) * 100
        );
    }
    $weekData[] = [

        "day" => date("D", strtotime($date)),
        "percent" => $percent
    ];
}


$topHabitQuery=mysqli_query($conn,
"SELECT habits.habit_name,
COUNT(habit_logs.id) as total

FROM habit_logs

JOIN habits
ON habit_logs.habit_id=habits.id

WHERE habit_logs.user_id='$user_id'
AND habit_logs.completed=1

AND YEARWEEK(habit_logs.completed_date,1)=YEARWEEK(
DATE_SUB(CURDATE(),INTERVAL $selectedWeek WEEK),1
)

GROUP BY habit_logs.habit_id

ORDER BY total DESC

LIMIT 1"
);

$topHabit = mysqli_fetch_assoc($topHabitQuery);


$categories=["Prayer","Athkar","Custom"];

$categoryPercentages=[];

foreach($categories as $category){

$totalCategoryHabitsQuery=mysqli_query($conn,
"SELECT COUNT(*) as total
FROM habits
WHERE user_id='$user_id'
AND category='$category'"
);

$totalCategoryHabits=mysqli_fetch_assoc(
$totalCategoryHabitsQuery
)['total'];

// $completedCategoryQuery=mysqli_query($conn,
// "SELECT COUNT(*) as done
// FROM habit_logs
// JOIN habits
// ON habit_logs.habit_id=habits.id
// WHERE habit_logs.user_id='$user_sid'
// AND habits.category='$category'
// AND habit_logs.completed=1"
// );

$completedCategoryQuery=mysqli_query($conn,
"SELECT COUNT(*) as done
FROM habit_logs
JOIN habits
ON habit_logs.habit_id=habits.id
WHERE habit_logs.user_id='$user_id'
AND habits.category='$category'
AND habit_logs.completed=1
AND YEARWEEK(habit_logs.completed_date,1)=YEARWEEK(
DATE_SUB(CURDATE(),INTERVAL $selectedWeek WEEK),1
)"
);

$completedCategoryHabits=mysqli_fetch_assoc(
$completedCategoryQuery
)['done'];

$percentage=0;

if($totalCategoryHabits>0){

$percentage=round(
($completedCategoryHabits/$totalCategoryHabits)*100
);
}

$categoryPercentages[]=$percentage;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title>Profile</title>

  <link
    rel="stylesheet"
    href="../css/profile.css"
    >

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

  <div class="profile-container">
    <div class="user-card">
    <div class="profile-layout">
        <div class="profile-avatar-container">
            <div class="profile-avatar">
                <i class="fa-solid fa-circle-user"></i>
            </div>
        </div>

        <div class="profile-details-fields">
            <div class="info-row">
                <span class="field-label">Name</span>
                <div class="field-value"><?php echo $user['name']; ?></div>
            </div>
            <div class="info-row">
                <span class="field-label">Email</span>
                <div class="field-value"><?php echo $user['email']; ?></div>
            </div>
            <div class="info-row">
                <span class="field-label">Password</span>
                <div class="field-value">••••••••</div>
            </div>

            <div class="settings-buttons">
                <button class="settings-btn" id="openPasswordModal">
                    Change Password
                </button>
                <button class="settings-btn logout-btn">
                    Logout
                </button>
            </div>
        </div>
    </div>
</div>
 


    <div class="stats-grid">

      <div class="stat-card">
        <i class="fa-regular fa-circle-check" style="color:#88986d; margin-right:8px;margin-left:10px; margin-top:30px ;font-size:3rem ;"></i>
        <h3>
          Completed Habits
        </h3>
    
        <p>
          <?php echo $totalCompletedHabits; ?>
        </p>

          <span class="habit">
           From All Habits
        </span>
         <!-- <i class="fa-regular fa-circle-check" style="color:#88986d; margin-right:8px;margin-left:10px ;font-size:3rem;"></i> -->
      </div>
      <div class="stat-card">
        <i class="fa-solid fa-book" style="color:#88986d; margin-right:8px;margin-left:10px; margin-top: 30px ; font-size:3rem ;"></i>
        <h3>
          Number of books
        </h3>

        <p>
          16
        </p>

         <span class="library">
           From Library
        </span>

      </div>

     <div class="stat-card">
      <!-- <div class="d-icon" style="color:#88986d;">
        <img src="img/journal.png" style="color:#88986d; margin-right:8px;margin-left:10px; margin-top: 30px ; font-size:3rem ;"> -->
        <i class="fa-solid fa-book-open" style="color:#88986d; margin-right:8px;margin-left:10px; margin-top: 30px ; font-size:2.8rem ;"></i>
      
       <h3>
        Journal Entries
       </h3>
       <p id="journalEntries">
         0
       </p>
       <div id="journalTags" class="journal-tags-stats"></div>
     </div>

      <div class="stat-card">
        <i class="fa-solid fa-list-check" style="color:#88986d; margin-right:8px;margin-left:10px; margin-top: 30px ; font-size:2.8rem ;"></i>
        <h3>
          Tasks Completed
        </h3>

        <p>
          <?php echo $totalCompletedTasks; ?>
        </p>

        <span class="tasks-total">
           Out of <?php echo $totalTasks; ?> Tasks
        </span>

      </div>
    </div>

    <div class="charts-container">
    <div class="chart-box">
        <h3>
            Weekly Tracker Progress
        </h3>

        <select id="weekSelect" class="week-select">
              <option value="0">This Week</option>
              <option value="1">Last Week</option>
              <option value="2">2 Weeks Ago</option>
        </select>
      
       <canvas id="weeklyChart" height="120"></canvas>
        <p class="top-habit">
          <i class="fa-solid fa-trophy"></i>
            Most Completed Habit:
           <strong>
            <?php
            echo $topHabit
            ? $topHabit['habit_name']
            : "No habits completed";
             ?>
          </strong>

          </p>
    </div>
    <div class="chart-box">

<h3>
Habits Categories Completion
</h3>

<div class="pie-layout">

<div class="pie-chart-wrapper">
<canvas id="pieChart"></canvas>
</div>
<div class="pie-details">

<div class="pie-item">
<div class="pie-color prayer-color"></div>
<span>
Prayer (<?php echo $categoryPercentages[0]; ?>%)
</span>
</div>

<div class="pie-item">
<div class="pie-color athkar-color"></div>
<span>
Athkar & Sunnan (<?php echo $categoryPercentages[1]; ?>%)
</span>
</div>

<div class="pie-item">
<div class="pie-color custom-color"></div>
<span>
My Habits (<?php echo $categoryPercentages[2]; ?>%)
</span>
</div>

</div>

</div>

</div>

</div><div id="passwordModal" class="modal-overlay">

<div class="modal">

<h2 class="modal-title">
Change Password
</h2>

<form action="changePassword.php" method="POST">

<input
     type="password"
     name="newPassword"
     class="habit-input"
     placeholder="Enter new password" required >

<div class="confirmation-buttons">

<button
type="button"
id="cancelPasswordBtn"
class="confirmation-btn cancel-btn">
Cancel
</button>

<button
type="submit"
class="confirmation-btn confirm-btn"> Save</button>
</div>

</form>
</div>
</div>

<script>

const weekData=<?php echo json_encode($weekData); ?>;

const labels=weekData.map(item=>item.day);

const percentages=weekData.map(item=>item.percent);

const weeklyCtx=document.getElementById("weeklyChart");

new Chart(weeklyCtx,{
type:'bar',

data:{
labels:labels,

datasets:[{
label:'Completion %',
data:percentages,
backgroundColor:'#88986d',
borderRadius:12
}]
},

options:{
responsive:true,

plugins:{
legend:{
display:false
}
},

scales:{
y:{
beginAtZero:true,
max:100
}
}
}
});
const pieCtx=document.getElementById("pieChart");

new Chart(pieCtx,{
type:'doughnut',

data:{
labels:[
'Prayer',
'Athkar',
'Custom'
],

datasets:[{
data:[
<?php echo $categoryPercentages[0]; ?>,
<?php echo $categoryPercentages[1]; ?>,
<?php echo $categoryPercentages[2]; ?>
],

backgroundColor:[
'#88986d',
'#5F7D7A',
'#F3C3B2'
],

borderWidth:0
}]
},

options:{
responsive:true,

plugins:{
legend:{
display:false
}
},

cutout:'65%'
}
});


const weekSelect=document.getElementById("weekSelect");

weekSelect.value=<?php echo $selectedWeek; ?>;
weekSelect.addEventListener("change",function(){
window.location.href=
"profile.php?week="+this.value;

});

 const passwordModal= document.getElementById("passwordModal");
const openPasswordBtn= document.getElementById("openPasswordModal");
const cancelPasswordBtn=document.getElementById("cancelPasswordBtn");
openPasswordBtn.addEventListener("click",()=>{
passwordModal.style.display="flex";
});

cancelPasswordBtn.addEventListener("click",()=>{

passwordModal.style.display="none";

});

//journal statistics
fetch('/Madar_Space_SA/php/jouranl_stat.php')
    .then(response => response.json())
    .then(data => {
        // Update total entries
        document.getElementById('journalEntries').textContent = data.totalEntries;
        
        
        const tagContainer = document.getElementById('journalTags');
        const tagNames = {
            'ideas': '<i class="fas fa-lightbulb"></i> Ideas',
            'work': '<i class="fas fa-briefcase"></i> Work',
            'personal': '<i class="fas fa-user"></i> Personal',
            'reminders': '<i class="fas fa-bell"></i> Reminder'
        };
        
        let tagsHTML = '<div class="journal-tags-breakdown">';
        for (let tag in data.tagStats) {
            const stat = data.tagStats[tag];
            tagsHTML += `
                <div class="tag-stat">
                    <span class="tag-name">${tagNames[tag]}</span>
                    <span class="tag-percentage">${stat.percentage}%</span>
                </div>
            `;
        }
        tagsHTML += '</div>';
        
        tagContainer.innerHTML = tagsHTML;
    })
    .catch(error => console.error('Error loading journal stats:', error));









</script>

</body>
</html>