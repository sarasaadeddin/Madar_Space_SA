<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
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


?>




<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Habit Tracker</title>
  <link rel="stylesheet" href="css/tracker.css" />
</head>
<body>
  <nav class="navbar">
  <div class="nav-container">

   <a href="index.html" class="logo">Madar<span>.</span></a>

    <nav class="nav">
      <ul class="nav-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="tasks.html">To-Do</a></li>
        <li><a href="journal.html">Journal</a></li>
        <li><a href="library.html">Library</a></li>
        <li><a href="prayer.html">Tracker</a></li>
      </ul>
    </nav>

    <div class="nav-icons">
      <a href="#"><i class="fas fa-user"></i></a>

      <div class="menu-toggle" id="menu-toggle">
        <i class="fa-solid fa-bars"></i>
      </div>
    </div>

  </div>
</nav>


  <header class="header">
    
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
  

  </header>

 
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
        <div id="custom-list"></div>
        <button class="add-btn" id="add-habit-btn">+ Add Habit</button>
      </div>
    </div>
  </section>

</main>

  

  <script src="js/tracker.js"></script>
</body>
</html>

