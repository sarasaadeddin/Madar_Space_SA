<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include("connect.php");

if(!isset($_SESSION['user_id'])){
    exit();
}

$user_id = $_SESSION['user_id'];

$habit_id = $_POST['habit_id'];
$complete = $_POST['completed'];
$date = $_POST['date'];

$check = mysqli_query($conn,

    "SELECT * FROM habit_logs
     WHERE user_id='$user_id'
     AND habit_id='$habit_id'
     AND completed_date='$date'"

);

if(mysqli_num_rows($check) > 0){

    mysqli_query($conn,

        "UPDATE habit_logs
         SET completed='$complete'
         WHERE user_id='$user_id'
         AND habit_id='$habit_id'
         AND completed_date='$date'"
    );

}else{

    mysqli_query($conn,

        "INSERT INTO habit_logs
        (user_id, habit_id, completed_date, completed)

        VALUES
        ('$user_id','$habit_id','$date','$complete')"
    );
}

echo "success";

?>