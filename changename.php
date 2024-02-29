<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);


if (isset($_POST['email']) && isset($_POST['name'])) {
    
$email=$_POST['email'];
$name=$_POST['name'];

$db->changename($email,$name);
}
else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters is missing!";
    echo json_encode($response);
}


?>