<?php

include "../connect.php";

$password = sha1($_POST['password']);
$email = filterRequest("email");
$phone = filterRequest("phone");
$name = filterRequest("name");
$verfiycode = rand(1000 , 9999);

// $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$stmt = $con->prepare(" SELECT * FROM customers WHERE customers_email = ? OR customers_phone =? ");
$stmt->execute(array($email , $phone ));
$count = $stmt->rowCount();

if ($count > 0){
    PrintFailure();
} else {
    $data = array(
        "customers_name" => $name,
        "customers_password" => $password,
        "customers_email" => $email,
        "customers_phone" => $phone,
        "customers_otp" => $verfiycode,
    );
    SendEmail($email , "Your Verfiy Code From Rise RealEstate" , "Your Verfiy Code Is $verfiycode ");

    insertData("customers" , $data);
}