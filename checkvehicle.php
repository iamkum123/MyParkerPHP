<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);


if (isset($_POST['vehicle'])) {
    
$vehicle=$_POST['vehicle'];

$db->checkvehicle($vehicle);
}



?>