<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $userEmail=$_POST['email'];
    $message=$_POST['message'];

    $mail = new PHPMailer(true);

    try{

        $mail->isSMTP();

        $mail->Host='smtp.gmail.com';

        $mail->SMTPAuth=true;

        $mail->Username='hyutheerreal@gmail.com';

        $mail->Password='cqjt xpmg vfoz iazg';

        $mail->SMTPSecure='tls';

        $mail->Port=587;

        // $mail->setFrom($userEmail);

        $mail->setFrom('hyutheerreal@gmail.com', 'Madar Contact Form');
        $mail->addReplyTo($userEmail);

        $mail->addAddress('hyutheerreal@gmail.com');

        $mail->Subject='New Contact Message From Madar';

        $mail->Body=
        "Sender Email: $userEmail\n\nMessage:\n$message";

        $mail->send();

       header('Content-Type: application/json');

echo json_encode([
    "success" => true,
    "message" => "Message Sent Successfully!"
]);
exit;

    }catch(Exception $e){

      header('Content-Type: application/json');

    echo json_encode([
    "success" => false,
    "message" => "Failed To Send Message"
     ]);
      exit;
    }
}
?>