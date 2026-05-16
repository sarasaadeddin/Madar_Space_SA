<?php

session_start();

include "connect.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        header("Location: ../home.php");
        exit();

    } else {

        // echo "Wrong password";
         header("Location: ../login.html?error=wrongpassword");
        exit();

    }

} else {

     header("Location: ../login.html?error=nouser");
    exit();

}

$conn->close();

?>