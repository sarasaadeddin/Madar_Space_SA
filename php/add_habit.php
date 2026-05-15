<?php


  session_start();
  include("connect.php");

  if(!isset($_SESSION['user_id'])){
    exit();
}

  $user_id = $_SESSION['user_id'];

  $name = $_POST['habit_name'];

  mysqli_query($conn,

    "INSERT INTO habits
    (user_id, habit_name, category)

    VALUES
    ('$user_id','$name','custom')"
);

echo mysqli_insert_id($conn);






?>