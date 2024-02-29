<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);


if (isset($_POST['email'])&& isset($_POST['topup'])) {
    
$email=$_POST['email'];
$topup=$_POST['topup'];

$user=$db->topupBalance($email,$topup);

if($user){
    $db->showBalance($email);
    //echo"ok";
}
}



?>