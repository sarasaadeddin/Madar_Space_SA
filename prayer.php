<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

include("php/connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");


?>



<?php

 

$defaultHabits = [

    // prayers
    ["Fajr", "prayer"],
    ["Dhuhr", "prayer"],
    ["Asr", "prayer"],
    ["Maghrib", "prayer"],
    ["Isha", "prayer"],

    // athkar
    ["Morning Athkar", "athkar"],
    ["Evening Athkar", "athkar"],
    ["Duha Prayer", "athkar"],
    ["Tahajjud", "athkar"]

];

foreach($defaultHabits as $habit){

    $habit_name = $habit[0];
    $category = $habit[1];

    $check = mysqli_query($conn,
        "SELECT * FROM habits 
         WHERE user_id='$user_id'
         AND habit_name='$habit_name'"
    );

    if(mysqli_num_rows($check) == 0){

        mysqli_query($conn,
            "INSERT INTO habits(user_id, habit_name, category)
             VALUES('$user_id','$habit_name','$category')"
        );
    }
}


$prayers = mysqli_query($conn,
    "SELECT * FROM habits
     WHERE user_id='$user_id'
     AND category='prayer'"
);

$athkar = mysqli_query($conn,
    "SELECT * FROM habits
     WHERE user_id='$user_id'
     AND category='athkar'"
);

$custom = mysqli_query($conn,
    "SELECT * FROM habits
     WHERE user_id='$user_id'
     AND category='custom'"
);

$today = $date;

$logs = mysqli_query($conn,
    "SELECT * FROM habit_logs
     WHERE user_id='$user_id'
     AND completed_date='$today'"
);

$completed = [];

while($row = mysqli_fetch_assoc($logs)){
    $completed[$row['habit_id']] = $row['completed'];
}


?>




<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Habit Tracker</title>
  <link rel="stylesheet" href="css/tracker.css" />
   <link rel="stylesheet" href="css/header.css" />
   <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="tracker-page">

  <?php include "include/header.php"; ?>
  

  <section class="tracker-header">
    
    <h1> Habit Tracker</h1>

    
    <div class="date-nav">
      <button class="nav-btn" id="prev-day">&#8249;</button>
      <span class="current-date" id="current-date"></span>
      <button class="nav-btn" id="next-day">&#8250;</button>
    </div>
      

   <div class="progress-card">
  
  <div class="progress-info">
    <span id="progress-count">0 / 0 Completed</span>

    <div class="progress-bar">
      <div class="progress-fill" id="progress-fill"></div>
    </div>
  </div>

  <div class="progress-circle">
    <span id="progress-percent">0%</span>
  </div>

</div>
  

</section>

 
 <main class="main-content">

  <section class="habits-section">
    <div class="habits-grid">

      
      <div class="habit-card">
        <div class="card-image prayer-img"></div>
      
        <h3>🕌 Prayers</h3>
        <!-- <a href="https://www.flaticon.com/free-icons/pray" title="pray icons">Pray icons created by Freepik - Flaticon</a> -->
        <div id="prayers-list">
          <?php while($row = mysqli_fetch_assoc($prayers)) { ?>

         <div class="habit">

        <input 
            type="checkbox"
            class="habit-checkbox"
               data-id="<?php echo $row['id']; ?>"
              <?php if(isset($completed[$row['id']]) && $completed[$row['id']] == 1) echo "checked"; ?>

         >

        <span>
            <?php echo $row['habit_name']; ?>
        </span>

    </div>

       <?php } ?>
        </div>

      </div>

      <!-- ATHKAR -->
      <div class="habit-card">
        <div class="card-image athkar-img"></div>
        <h3>📿 Athkar & Sunan</h3>
        <div id="athkar-list">
                  <?php while($row = mysqli_fetch_assoc($athkar)) { ?>

         <div class="habit">

        <input 
            type="checkbox"
            class="habit-checkbox"
               data-id="<?php echo $row['id']; ?>"
              <?php if(isset($completed[$row['id']]) && $completed[$row['id']] == 1) echo "checked"; ?>

         >

        <span>
            <?php echo $row['habit_name']; ?>
        </span>

         </div>

       <?php } ?>



        </div>
      </div>

      <!-- CUSTOM -->
      <div class="habit-card">
         <div class="card-image myhabit-img"></div>
        <h3>📌 My Habits</h3>
        <div id="custom-list">
          <?php while($row = mysqli_fetch_assoc($custom)) { ?>

       <div class="habit">

       <input 
         type="checkbox"
         class="habit-checkbox"
         data-id="<?php echo $row['id']; ?>"
         <?php 
         if(isset($completed[$row['id']]) 
         && $completed[$row['id']] == 1) 
         echo "checked"; 
          ?>
         > 

        <span>
            <?php echo $row['habit_name']; ?>
        </span>

       </div>

       <?php } ?>
        </div>
        <button class="add-btn" id="add-habit-btn">+ Add Habit</button>
      </div>
    </div>

  </section>


  <button class="progress-btn" id="showWeeklyBtn">
  View Weekly Progress
</button>

</main>



<div id="habitModal" class="modal-overlay">

  <div class="modal">

    <div class="modal-body">

      <h2 class="modal-title">
        Add New Habit
      </h2>

      <input 
        type="text"
        id="habit-name-input"
        class="habit-input"
        placeholder="Enter habit name"
      >

      <div class="confirmation-buttons">

        <button id="cancelHabitBtn"
                class="confirmation-btn cancel-btn">
          Cancel
        </button>

        <button id="saveHabitBtn"
                class="confirmation-btn confirm-btn">
          Add Habit
        </button>

      </div>

    </div>

  </div>

</div>


  <div id="weeklyModal" class="modal-overlay">

  <div class="modal weekly-modal">

    <div class="modal-body">

      <h2 class="modal-title">
        Weekly Progress
      </h2>

      <div id="weekly-progress">

      </div>

      <div class="confirmation-buttons">

        <button
          id="closeWeeklyBtn"
          class="confirmation-btn confirm-btn">

          Close

        </button>

      </div>

    </div>

      </div>

</div>

  
   
  <script>

     let selectedDate = "<?php echo $date; ?>";

</script>
  <script src="js/tracker.js"></script>
  <?php include "include/footer.php"; ?>
</body>
</html>

