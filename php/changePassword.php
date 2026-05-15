<?php

session_start();

include("connect.php");

if(!isset($_SESSION['user_id'])){
header("Location: login.html");
exit();
}

$user_id=$_SESSION['user_id'];

$newPassword=$_POST['newPassword'];

$hashedPassword=password_hash(
$newPassword,
PASSWORD_DEFAULT
);

mysqli_query($conn,
"UPDATE users
SET password='$hashedPassword'
WHERE id='$user_id'"
);

header("Location: profile.php");
exit();

?>