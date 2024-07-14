<?php

include "../connect.php" ;

$email = filterRequest("email");

$verfiycode = rand(1000 , 9999);

$data = array(
    "customers_otp" => $verfiycode 
) ;

updateData("customers" , $data , "customers_email = '$email'" ) ;

SendEmail($email , "Your Verfiy Code From BlubleStore" , "Your Verfiy Code Is $verfiycode ");

