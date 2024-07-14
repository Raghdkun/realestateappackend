<?php

include "../connect.php" ;

$email = filterRequest("email");

$verfiycode = rand(1000 , 9999);

$data = array(
    "users_otp" => $verfiycode 
) ;

updateData("users" , $data , "users_email = '$email'" ) ;

SendEmail($email , "Your Verfiy Code From BlubleStore" , "Your Verfiy Code Is $verfiycode ");

