<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);


if (isset($_POST['email'])&&isset($_POST['location'])&&isset($_POST['vehicle'])&&isset($_POST['duration'])&&isset($_POST['total'])) {
    
$email=$_POST['email'];
$location=$_POST['location'];
$vehicle=$_POST['vehicle'];
$duration=$_POST['duration'];
$total=$_POST['total'];

$db->payparking($email,$location,$vehicle,$duration,$total);
}
else{
    $response["error"]=true;
    $response["error_msg"]="Please enter all credentials";
    echo json_encode($response);
}



?>