<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);


if (isset($_POST['officername']) && isset($_POST['name']) && isset($_POST['vehicle']) && isset($_POST['location']) && isset($_POST['offense'])&& isset($_POST['price'])) {
    
$officername=$_POST['officername'];
$username=$_POST['name'];
$vehicle=$_POST['vehicle'];
$location=$_POST['location'];
$offense=$_POST['offense'];
$price=$_POST['price'];



$db->addsummon($officername, $username, $vehicle, $location, $offense,$price);
}
else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters is missing!";
    echo json_encode($response);
}



?>