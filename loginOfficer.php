<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['email']) && isset($_POST['password'])) {

    // receiving the post params
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // get the user by email and password
    $officer = $db->getOfficerByEmailAndPassword($email, $password);

    if ($officer != false) {
        // user is found
        $response["error"] = FALSE;
        $response["uid"] = $officer["Officer_ID"];
        $response["user"]["name"] = $officer["Officer_Name"];
        $response["user"]["address"] = $officer["Officer_Address"];
        $response["user"]["gender"] = $officer["Officer_Gender"];
        $response["user"]["email"] = $officer["Officer_Email"];
		$response["user"]["mobile"] = $officer["Officer_MobileNumber"];
		
                    //$response["user"]["created_at"] = $user["created_at"];
					// $response["user"]["dob"] = $officer["User_DOB"];
                    //$response["user"]["updated_at"] = $user["updated_at"];

        echo json_encode($response);
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Login credentials are wrong. Please try again!";
        echo json_encode($response);
    }
} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters email or password is missing!";
    echo json_encode($response);
}
?>

