<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);


if (isset($_POST['email']) && isset($_POST['amount'])&& isset($_POST['summonid'])) {
    
$email=$_POST['email'];
$amount=$_POST['amount'];
$id=$_POST['summonid'];

$db->paysummon($email,$amount,$id);
}
else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters is missing!";
    echo json_encode($response);
}


?>