<?php

session_start();

include("connect.php");

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$start = $_GET['start'];

$result = [];



for($i = 0; $i < 7; $i++){

    $date = date(
        "Y-m-d",
        strtotime($start . " +$i days")
    );



   

    $totalQuery = mysqli_query($conn,
        "SELECT COUNT(*) as total
         FROM habits
         WHERE user_id='$user_id'"
    );

    $totalRow = mysqli_fetch_assoc($totalQuery);

    $total = $totalRow['total'];



    
    $doneQuery = mysqli_query($conn,
        "SELECT COUNT(*) as done
         FROM habit_logs
         WHERE user_id='$user_id'
         AND completed=1
         AND completed_date='$date'"
    );

    $doneRow = mysqli_fetch_assoc($doneQuery);

    $done = $doneRow['done'];



  

    $percent = 0;

    if($total > 0){

        $percent = round(
            ($done / $total) * 100
        );
    }



    $result[] = [

        "date" => $date,
        "percent" => $percent
    ];
}



echo json_encode($result);

?>