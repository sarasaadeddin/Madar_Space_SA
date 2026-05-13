<?php

include "connect.php";

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password)
VALUES ('$name', '$email', '$hashedPassword')";

if ($conn->query($sql) === TRUE) {

    header("Location: ../login.html");

} else {

    echo "Error: " . $conn->error;

}

$conn->close();

?>