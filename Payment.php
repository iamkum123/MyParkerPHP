<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['summon']) && isset($_POST['location'])&& isset($_POST['vehicle'])&& isset($_POST['duration'])) {
    
    $summon=$_POST['summon'];
    summon($summon);
}



?>