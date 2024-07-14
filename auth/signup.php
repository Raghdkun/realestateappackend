<?php

include "../connect.php";

$password = sha1($_POST['password']);
$email = filterRequest("email");
$phone = filterRequest("phone");
$name = filterRequest("name");
$verfiycode = rand(1000 , 9999);

// $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$stmt = $con->prepare(" SELECT * FROM users WHERE users_email = ? OR users_phone =? ");
$stmt->execute(array($email , $phone ));
$count = $stmt->rowCount();

if ($count > 0){
    PrintFailure();
} else {
    $data = array(
        "users_name" => $name,
        "users_password" => $password,
        "users_email" => $email,
        "users_phone" => $phone,
        "users_otp" => $verfiycode,
    );
    SendEmail($email , "Your Verfiy Code From Rise RealEstate" , "Your Verfiy Code Is $verfiycode ");

    insertData("users" , $data);
}