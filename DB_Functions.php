<?php


class DB_Functions {

    private $conn;

    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {
        
    }

    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $password,$dob,$address,$gender,$mobile) {
        //$uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $stmt = $this->conn->prepare("INSERT INTO user(User_Name,User_DOB, User_Email,User_Address, User_Gender,User_MobileNumber,encrypted_password,salt,created_at) VALUES( ?, ?, ?, ?,?,?,?,?,NOW())");
        $stmt->bind_param("sssssdss", $name, $dob, $email, $address,$gender,$mobile,$encrypted_password,$salt);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM user WHERE User_Email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

        //initialize balance of new user as 0
        $stmt = $this->conn->prepare("INSERT INTO balance (User_ID, balance)VALUES(?,?)");
        $initial=0;
            $stmt->bind_param("sd", $user['User_ID'],$initial);
            $stmt->execute();	
            $stmt->close();
            
            
            return $user;
        } else {
            return false;
        }
    }

    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {

        $stmt = $this->conn->prepare("SELECT * FROM user WHERE User_Email = ?");

        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // verifying user password
            $salt = $user['salt'];
            $encrypted_password = $user['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $user;
            }
        } else {
            return NULL;
        }
    }

    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $stmt = $this->conn->prepare("SELECT * from user WHERE User_Email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }
    
    public function showBalance($email){
        
        //$stmt = $this->conn->prepare("SELECT balance FROM balance e join user d on e.User_ID =d.User_ID WHERE d.User_Email=?");
        
        //$stmt->bind_param("s", $email);
        //$stmt="SELECT balance FROM balance e join user d on e.User_ID =d.User_ID WHERE d.User_Email=$email";
        /*$results = mysqli_query($this->conn,"SELECT * FROM balance e join user d on e.User_ID =d.User_ID WHERE d.User_Email=$email");
      if($results){echo"yes";}  
while ($row = mysqli_fetch_assoc($results)) {
    echo $row['balance'];*/
    
      $stmt = $this->conn->prepare("SELECT * FROM balance e join user d on e.User_ID =d.User_ID WHERE d.User_Email=?");

        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
    
    $response["error"]=false;
    $response["balance"]["balance"]=$user["balance"];
   // echo $user['balance'];
       	    echo json_encode($response);
    
}      
//$stmt->execute();
        //echo$stmt;
    }
    
    
    
     public function topupBalance($email,$topup){
    //update the balance of user in balance table
      $stmt = $this->conn->prepare("UPDATE balance b join user u on b.User_ID=u.User_ID SET b.balance=(?+b.balance) where u.User_Email=? ");
	  
        $stmt->bind_param("ds",$topup, $email);

        $stmt->execute();
            $stmt->close();
        
   // return true;
      
    // insert new id into topup table as receipt
    $stmt2 = $this->conn->prepare("INSERT INTO topup (User_ID,Amount) select User_ID,? from user where User_Email=?");
	
        $stmt2->bind_param("ds", $topup,$email);
        $stmt2->execute();
            $stmt2->close();
        
    //return true;
  
return true;

    }
    
    public function showPaymentData($email){
        //get ID based on the email
		$stmt = $this->conn->prepare("SELECT * from user where User_Email=?  ");
	    $stmt->bind_param("s", $email);
        $stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
			$ID=$user["User_ID"];
		
		// get data from the payment table based on the ID 
		$stmt2 = $this->conn->prepare("SELECT l.Location_Name,p.Start_Time,p.End_Time,p.Charges,v.Vehicle_Number 
		from payment p join location l on l.Location_ID=p.Location_ID join vehicle v on p.Vehicle_ID=v.Vehicle_ID where p.User_ID=? ");
		$stmt2->bind_param("s", $ID);
		$stmt2->execute();		
		$stmt2->bind_result($location,$starttime,$endtime,$price,$vehicle);
		//$stmt2->close();

    	date_default_timezone_set("Asia/Kuala_Lumpur");
		$curtime=date("Y-m-d H:i:s");
		$paymentsList=array();
		
		while($stmt2->fetch()){
		$temp=array();
		

		$temp['Location']=$location;
		$temp['Price']=$price;
		$temp['Start_Time']=$starttime;
		$temp['End_Time']=$endtime;
		$temp['Vehicle']=$vehicle;
	if($endtime>$curtime){
	    $status="Active";
		$temp['Status']=$status;
    }
    else{
         $status="Expired";
		$temp['Status']=$status;
    }
		array_push($paymentsList,$temp);

		}
        $stmt2->close();

		echo json_encode($paymentsList);
			
    }
    
    public function showSummonData($email){
		
	$stmt = $this->conn->prepare("SELECT * from user where User_Email=?  ");
	    $stmt->bind_param("s", $email);
        $stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			$ID=$user["User_ID"];

			//get data from summon table based on ID
		$stmt = $this->conn->prepare("Select s.Summon_ID,l.Location_Name,v.Vehicle_Number
		,s.Charges,s.Descriptions,s.Date,s.Status from summon s 
		join location l on l.Location_ID=s.Location_ID join vehicle v 
		on s.Vehicle_ID=v.Vehicle_ID where s.User_ID=?  order by Date ASC");
		$stmt->bind_param("s", $ID);
		$stmt->execute();		
		$stmt->bind_result($id,$location,$vehicle,$price,$desc,$date,$status);
		
		$summonList=array();
		
		while($stmt->fetch()){
		$temp=array();
		
		$temp['id']=$id;
		$temp['location']=$location;
		$temp['date']=$date;
		$temp['charges']=$price;
		$temp['description']=$desc;
		$temp['status']=$status;
		$temp['vehicle']=$vehicle;

			
		array_push($summonList,$temp);

		}
		$stmt->close();
		echo json_encode($summonList);
	}
	
	    public function changename($email,$name){
			
		$stmt = $this->conn->prepare("UPDATE user SET User_Name=? where User_Email=?  ");
	    $stmt->bind_param("ss", $name,$email);
        $stmt->execute();
			$stmt->close();
			
			
			    $response["error"]=false;
			    echo json_encode($response);


}

public function changepassword($email,$oldpass,$newpass,$renewpass){
		
		$stmt = $this->conn->prepare("Select * from user where User_Email=?");
	    $stmt->bind_param("s",$email);
        $stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();
		$stmt->close();

		$encrypted_pass= $user['encrypted_password'];
		$salt= $user['salt'];
		$hash = $this->checkhashSSHA($salt, $oldpass);
		
		//check for password equality
		if($encrypted_pass==$hash){
		 $hash = $this->hashSSHA($renewpass);
		 $encrypted_password = $hash["encrypted"]; // encrypted password
	     $salt = $hash["salt"]; // salt

		$stmt = $this->conn->prepare("UPDATE user SET encrypted_password=?, salt=? where User_Email=?");
	    $stmt->bind_param("sss",$encrypted_password,$salt, $email);
        $stmt->execute();
		$stmt->close();
		
		$response["error"]=false;
		 echo json_encode($response);	
		}
		else{
			$response["error"]=true;
			$response["error_msg"]="The current password is incorrect";
			echo json_encode($response);	

			
		}
		
	}
	
	public function addvehicle($email,$vehicle){
	    $stmt = $this->conn->prepare("INSERT INTO vehicle (User_ID,Vehicle_Number,Status) select User_ID,?,? from user where User_Email=?");
	    $status="active";
	    $stmt->bind_param("sss",$vehicle,$status,$email);
       
       if( $stmt->execute()){
        
         $response["error"]=false;
			    echo json_encode($response);}
	}
	
	public function paysummon($email,$amount,$sid){
	     $stmt = $this->conn->prepare("SELECT * FROM balance e join user d on e.User_ID =d.User_ID 
	     WHERE d.User_Email=?");
	    $stmt->bind_param("s",$email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $balance=$user['balance'];
        $id=$user['User_ID'];
        
        if($balance>=$amount){
            
        $newbalance=$balance-$amount;
        $stmt = $this->conn->prepare("Update balance Set balance=? where User_ID=?");
	    $stmt->bind_param("ss",$newbalance,$id);
        
        if($stmt->execute()){
            $status="Paid";
            $stmt->close();
             $stmt = $this->conn->prepare("Update summon Set Status=? where Summon_ID=?");
	    $stmt->bind_param("ss",$status,$sid);
	    $stmt->execute();
	                $stmt->close();

                   $response["error"]=false;
            			    echo json_encode($response);

        }
                   else{
                   $response["error"]=true;
                $response["error_msg"]="Something wrong with the update function";       
                       			    echo json_encode($response);

                   }

            
        }
         else{
            $response["error"]=true;
                $response["error_msg"]="Your balance is insufficient to complete the process. Please topup";  
			    echo json_encode($response);
        }
        
	    
	}
	
	public function checkparkingprice($location){
	    $stmt = $this->conn->prepare("Select Charges from location where Location_Name=?");
	    $stmt->bind_param("s",$location);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        $response["error"]=false;
        $response['price']=$user['Charges'];
        echo json_encode($response);
	}
	
	
	
	public function checkvehicle($vehicle){

		$stmt2 = $this->conn-> prepare ("Select * from vehicle where Vehicle_Number=?");
		$stmt2->bind_param("s",$vehicle);
        $stmt2->execute();
        $user = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();
		
        if($user["Vehicle_Number"]==null){
               $status="car is not registered";
		       $response["error"]=false;
		       $name="No owner";
		       $response['name']=$name;
		       $response['status']=$status;
            echo json_encode($response);
        }else{
		    
			$stmt3 = $this->conn-> prepare ("Select u.User_Name, p.End_Time from user u join vehicle v on u.User_ID=v.User_ID join payment p on p.Vehicle_ID=v.Vehicle_ID where v.Vehicle_Number =?");
			$stmt3->bind_param("s",$vehicle);
			$stmt3->execute();
			$user2 = $stmt3->get_result()->fetch_assoc();
			
		
			if($user2==null){
					$stmt4 = $this->conn-> prepare ("Select u.User_Name from user u join vehicle v where u.User_ID=v.User_ID and v.Vehicle_Number=?");
					$stmt4->bind_param("s",$vehicle);
					$stmt4->execute();
					$username = $stmt4->get_result()->fetch_assoc();
					$uname = $username['User_Name'];
					
					$status="inactive";
					$response["error"]=false;
					$response["name"]=$uname;
					$response['status']=$status;
						echo json_encode($response);
			}
			if($user2!=null) {
    		$stmt = $this->conn-> prepare ("Select u.User_Name, p.End_Time from user u join vehicle v on u.User_ID=v.User_ID join payment p on p.Vehicle_ID=v.Vehicle_ID where v.Vehicle_Number =?");
    	    $stmt->bind_param("s",$vehicle);
            $stmt->execute();
            $stmt->bind_result($name,$endtime);
        
			date_default_timezone_set("Asia/Kuala_Lumpur");
			$curtime=date("Y-m-d H:i:s");
			
			$paymentList=array();
			$status="inactive";
		
			while($stmt->fetch()){
				$temp=array();
				
				$temp['name']=$name;
				$temp['endtime']=$endtime;

				if($temp['endtime']>=$curtime){
				//array_push($paymentList,$temp);
				$status="active";
				$response["error"]=false;
				$response['name']=$name;
				$response['status']=$status;
					echo json_encode($response);
				}
			
			}
			
				if($status!="active"){
						$status="inactive";
				$response["error"]=false;
				$response['name']=$name;
				$response['status']=$status;
					echo json_encode($response);
				}
			}
		}
	}

	
	
	public function showvehicle($email){
	    	$stmt = $this->conn->prepare("SELECT * from user where User_Email=?  ");
	    $stmt->bind_param("s", $email);
        $stmt->execute();
			$user = $stmt->get_result()->fetch_assoc();
			$stmt->close();
			$ID=$user["User_ID"];

			//get data from vehicle table based on ID
		$status="active";	
		$stmt = $this->conn->prepare("Select Vehicle_Number from vehicle where User_ID=? and Status=?");
		$stmt->bind_param("ss",$ID,$status);
		$stmt->execute();		
		$stmt->bind_result($vehicleno);
		
		$vehicleList=array();
		while($stmt->fetch()){
		$temp=array();
		
		$temp['vehicle']=$vehicleno;

		array_push($vehicleList,$temp);

		}
		$stmt->close();
		echo json_encode($vehicleList);
	}
	
    	public function deletevehicle($email,$vehicle){
    	    
	    $stmt = $this->conn->prepare("SELECT * from user where User_Email=?  ");
	    $stmt->bind_param("s", $email);
        $stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();
	    $stmt->close();
		$ID=$user["User_ID"];
		
			//delete vehicle based on user id and vehicle number
		$status="inactive";
		$stmt = $this->conn->prepare("Update vehicle set Status=? where User_ID=? and Vehicle_Number=?");
		$stmt->bind_param("sss",$status, $ID,$vehicle);
		if($stmt->execute()){
		    $stmt->close();
		    $response["error"]=false;
		    echo json_encode($response);
		    
		}	
	}
	
	public function payparking($email,$location,$vehicle,$duration,$total){
    	    
	    $stmt = $this->conn->prepare("SELECT * from user where User_Email=?  ");
	    $stmt->bind_param("s", $email);
        $stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();
	    $stmt->close();
		$userID=$user["User_ID"];
		
		$stmt = $this->conn->prepare("SELECT * from balance where User_ID=?  ");
	    $stmt->bind_param("s", $userID);
        $stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();
	    $stmt->close();
		$balance=$user["balance"];
		
		if($balance<$total){
		    $response["error"]=true;
		    $response["error_msg"]="You have insufficient balance. Please topup";
		    echo json_encode($response);
		}else{

	    $stmt = $this->conn->prepare("SELECT * from vehicle where Vehicle_Number=?  ");
	    $stmt->bind_param("s", $vehicle);
        $stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();
	    $stmt->close();
		$vehicleID=$user["Vehicle_ID"];
		
		$stmt = $this->conn->prepare("SELECT * from location where Location_Name=?  ");
	    $stmt->bind_param("s", $location);
        $stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();
	    $stmt->close();
		$locationID=$user["Location_ID"];
		
		date_default_timezone_set("Asia/Kuala_Lumpur");
		//$addtime="strtotime('+"+$duration+"hours')";
		$starttime=date("Y-m-d H:i:s");
		$endtime = date("Y-m-d H:i:s",strtotime("+{$duration} hours"));

        //Insert payment data into table
		$stmt = $this->conn->prepare("INSERT into payment (User_ID,Vehicle_ID,Location_ID,Start_Time,End_Time,Charges) VALUES(?,?,?,?,?,?)");
	    $stmt->bind_param("sssssd", $userID,$vehicleID,$locationID,$starttime,$endtime,$total);
       if($stmt->execute()){
           
           	    $stmt->close();
        //update new balance
        $stmt = $this->conn->prepare("UPDATE balance SET balance=? where User_ID=?  ");
        $newbalance=$balance-$total;
	    $stmt->bind_param("ds",$newbalance,$userID );
        $stmt->execute();
	    $stmt->close();
           	    $response["error"]=false;
           	    echo json_encode($response);

       }
      
		}
	}
	
	public function showparkingstatus($email){
    	    
	    $stmt = $this->conn->prepare("SELECT * from user where User_Email=?  ");
	    $stmt->bind_param("s", $email);
        $stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();
	    $stmt->close();
		$ID=$user["User_ID"];
		
			//get user parking payment info
		$stmt = $this->conn->prepare("Select v.Vehicle_Number,l.Location_Name,p.End_Time 
		from payment p join vehicle v on p.Vehicle_ID=v.Vehicle_ID join location l on p.Location_ID=l.Location_ID
		where p.User_ID=?");
		$stmt->bind_param("s",$ID);
		$stmt->execute();	
		$stmt->bind_result($vehicle,$location,$endtime);

		date_default_timezone_set("Asia/Kuala_Lumpur");
		$curtime=date("Y-m-d H:i:s");

		
		$paymentList=array();
		while($stmt->fetch()){
		$temp=array();
		
		$temp['vehicle']=$vehicle;
		$temp['location']=$location;
		$temp['endtime']=$endtime;
		
		

        if($temp['endtime']>=$curtime){
		array_push($paymentList,$temp);
        }
        

		}
			$stmt->close();
		echo json_encode($paymentList); 
	}

    
    public function checksummonstatus($email){
         $stmt = $this->conn->prepare("SELECT * from user where User_Email=?  ");
	    $stmt->bind_param("s", $email);
        $stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();
	    $stmt->close();
		$ID=$user["User_ID"];
		
			//get user parking summon info
		$stmt = $this->conn->prepare("Select Status from summon where User_ID=?");
		$stmt->bind_param("s",$ID);
		$stmt->execute();	
		$stmt->bind_result($status);

		
		$summonList=array();
		$response["response"]="no";//default value for summon = no = no outstanding summon

		while($stmt->fetch()){
		$temp=array();
		
		$temp['status']=$status;
	

        if($temp['status']=="Unpaid"){//if summon unpaid,response=yes
            $response["response"]="yes";
            $response["error"]=false;
        echo json_encode($response);
        break 1;
        }
        

		}
		//if no summon, response=no
		if($response["response"]!="yes"){
		    $response["error"]=false;
		    		  $response["response"]="no";
           	    echo json_encode($response);
		}

        
    }
    
           public function getOfficerByEmailAndPassword($email, $password) {

        $stmt = $this->conn->prepare("SELECT * FROM officer WHERE Officer_Email = ?");

        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $officer = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // verifying user password
			$officerPassword = $officer["Officer_Password"];

            if ($password == $officerPassword) {
                // user authentication details are correct
                return $officer;
            }
        } else {
            return NULL;
        }
    }
    
    public function addsummon($officername,$name, $vehicle, $location, $offense, $price){
	    //$stmt = $this->conn->prepare("INSERT INTO summon (User_ID,Officer_ID,Vehicle_ID,Location_ID,Descriptions,Price) select User_ID,?,? from user where User_Name=?");
	    $stmt = $this->conn->prepare("Select * from officer where Officer_Name=?");
	    $stmt->bind_param("s",$officername);
		$stmt->execute();
		$officer = $stmt->get_result()->fetch_assoc();
		$oid=$officer["Officer_ID"];
		$stmt->close();
		
	    $stmt = $this->conn->prepare("Select * from user where User_Name=?");
	    $stmt->bind_param("s",$name);
		$stmt->execute();
		$user = $stmt->get_result()->fetch_assoc();
		$uid=$user["User_ID"];
		$stmt->close();
		
		$stmt = $this->conn->prepare("Select * from vehicle where Vehicle_Number=?");
	    $stmt->bind_param("s",$vehicle);
		$stmt->execute();
		$vehiclenum = $stmt->get_result()->fetch_assoc();
		$vid=$vehiclenum["Vehicle_ID"];
		$stmt->close();
		
		$stmt = $this->conn->prepare("Select * from location where Location_Name=?");
	    $stmt->bind_param("s",$location);
		$stmt->execute();
		$locationname = $stmt->get_result()->fetch_assoc();
		$lid=$locationname["Location_ID"];
		$stmt->close();
		
		$stmt = $this->conn->prepare("INSERT INTO summon (User_ID, Vehicle_ID, Location_ID, Officer_ID, Charges, Descriptions, Date, Status ) VALUES(?,?,?,?,?,?,?,?)");
		$status="Unpaid";
		date_default_timezone_set("Asia/Kuala_Lumpur");
		//$addtime="strtotime('+"+$duration+"hours')";
		$starttime=date("Y-m-d H:i:s");
		
		$stmt->bind_param("ssssdsss",$uid,$vid,$lid,$oid,$price,$offense,$starttime,$status);


		if($stmt->execute()){
			$response["error"]=false;
				echo json_encode($response);
		}
		else{
		$response["error"]=true;
				echo json_encode($response);
		}

	}
    
	public function getlocation(){
    	    
	    $stmt = $this->conn->prepare("SELECT Location_Name from location ");
        $stmt->execute();
		$stmt->bind_result($location);

		$locationList=array();
		while($stmt->fetch()){
		$temp=array();
		
		$temp['location']=$location;
		array_push($locationList,$temp);
		    
		}	
		echo json_encode($locationList);
			    $stmt->close();

	}

}

?>
