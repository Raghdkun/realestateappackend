<?php

include "../connect.php" ;
$email = filterRequest("email") ;
$verfiy = filterRequest("verfiycode") ;


$stmt = $con->prepare("SELECT * FROM customers WHERE customers_email = '$email' AND customers_otp = '$verfiy' ") ;

$stmt->execute() ;

$count = $stmt->rowCount() ;

if ($count > 0){
 $data = array("customers_approve" => "1") ;

   updateData("customers" , $data , "customers_email = '$email'") ;

}else { 
printFailure("verfiy code is not correct");
}
