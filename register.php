<?php


require_once 'include/DB_Functions.php';
$db = new DB_Functions();

// json response array
$response = array("error" => FALSE);


if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])&& isset($_POST['dob'])&& isset($_POST['address'])&& isset($_POST['gender'])&& isset($_POST['mobile'])) {

    // receiving the post params
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $mobile = $_POST['mobile'];


    // check if user is already existed with the same email
    if ($db->isUserExisted($email)) {
        // user already existed
        $response["error"] = TRUE;
        $response["error_msg"] = "User already existed with " . $email;
        echo json_encode($response);
    } else {
        // create a new user
        $user = $db->storeUser($name, $email, $password,$dob,$address,$gender,$mobile);
        if ($user) {
            // user stored successfully
            $response["error"] = FALSE;
            $response["uid"] = $user["User_ID"];
            $response["user"]["name"] = $user["User_Name"];
            $response["user"]["dob"] = $user["User_DOB"];
            $response["user"]["email"] = $user["User_Email"];
             //$response["user"]["password"] = $user["password"];
            //$response["user"]["updated_at"] = $user["updated_at"];
            $response["user"]["address"] = $user["User_Address"];
            $response["user"]["gender"] = $user["User_Gender"];
            $response["user"]["mobile"] = $user["User_MobileNumber"];
             $response["user"]["created_at"] = $user["created_at"];

            
            echo json_encode($response);
        } else {
            // user failed to store
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in registration!";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (name, email or password) is missing!";
    echo json_encode($response);
}
?>

