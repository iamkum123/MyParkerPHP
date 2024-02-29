<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);


if (isset($_POST['email']) && isset($_POST['oldpass'])&& isset($_POST['newpass'])&& isset($_POST['renewpass'])) {
    
$email=$_POST['email'];
$oldpass=$_POST['oldpass'];
$newpass=$_POST['newpass'];
$renewpass=$_POST['renewpass'];


$db->changepassword($email,$oldpass,$newpass,$renewpass);
}
else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters is missing!";
    echo json_encode($response);
}



?>