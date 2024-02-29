<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);


if (isset($_POST['email'])) {
    
$email=$_POST['email'];

$db->checksummonstatus($email);
}
else{
    $response["error"]=true;
    $response["error_msg"]="error in retrieving status";
    echo json_encode($response);
}



?>