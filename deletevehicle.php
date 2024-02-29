<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);


if (isset($_POST['email'])&&isset($_POST['vehicle'])) {

$email=$_POST['email'];
$vehicle=$_POST['vehicle'];

$db->deletevehicle($email,$vehicle);
}



?>