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
    $user = $db->getUserByEmailAndPassword($email, $password);

    if ($user != false) {
        // user is found
        $response["error"] = FALSE;
        $response["uid"] = $user["User_ID"];
        $response["user"]["name"] = $user["User_Name"];
        $response["user"]["email"] = $user["User_Email"];
          $response["user"]["dob"] = $user["User_DOB"];
            $response["user"]["address"] = $user["User_Address"];
            $response["user"]["gender"] = $user["User_Gender"];
            $response["user"]["mobile"] = $user["User_MobileNumber"];
                    $response["user"]["created_at"] = $user["created_at"];

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

