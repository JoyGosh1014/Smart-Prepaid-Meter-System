<?php 
	session_start();

	// variable declaration
	$username = "";
	$user = "";
	$name = "";
	$email = "";
	$location = "";
	$mobile_number = "";
	$nid = "";
	$meter_number = "";
	$device_code = "";
	$national_id = "";
	$roll = "";
	$password = "";
	$errors = array(); 
	$add_location_errors = array(); 
	$add_location_success = array(); 
	$successes = array();
	$notification_errors = array();
	$notification_successes = array();
	$payment_add_successes = array();
	$payment_add_errors = array();
	$user_add_successes = array();
	$_SESSION['success'] = "";
	$reset_out = 0;
	$otp_out = 0;


	$tz = 'Asia/Dhaka';
    $tz_obj = new DateTimeZone($tz);
    $now = new DateTime("now", $tz_obj);
    $current_date = $now->format('Y-m-d');
    $current_time = $now->format('H:i');
    $current_month = $now->format('m');
    $current_year = $now->format('Y');

	// connect to database
	$db = mysqli_connect('localhost', 'oni_abir', 'E]R;z7i&lkR(', 'oni_pms');

	// REGISTER USER
	if (isset($_POST['add'])) {
		// receive all input values from the form
		$name = mysqli_real_escape_string($db, $_POST['name']);
		$location = mysqli_real_escape_string($db, $_POST['location']);
		$mobile_number = mysqli_real_escape_string($db, $_POST['mobile_number']);
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$meter_number = mysqli_real_escape_string($db, $_POST['meter_number']);
		$device_code = mysqli_real_escape_string($db, $_POST['device_code']);
		$national_id = mysqli_real_escape_string($db, $_POST['national_id']);
		

		// form validation: ensure that the form is correctly filled
		if (empty($name)) { array_push($errors, "Name is required"); }
		if (empty($location)) { array_push($errors, "Location is required"); }
		if (empty($mobile_number)) { array_push($errors, "Mobile Number is required"); }
		if (empty($email)) { array_push($errors, "Email is required"); }
		if (empty($meter_number)) { array_push($errors, "Meter Number is required"); }
		if (empty($device_code)) { array_push($errors, "Device Code is required"); }
		if (empty($national_id)) { array_push($errors, "NID Number is required"); }
		
			$query2 = "SELECT location FROM location WHERE loc_id = $location";
    		$result2 = mysqli_query($db,$query2);
    		// while($row = mysqli_fetch_assoc($result2)) {
				  //   $location = $row['location'];
				  // } 
    		$row = mysqli_fetch_assoc($result2);
    		 $location = $row['location'];
			$username = $location.$meter_number;
			$username = str_replace(' ', '', $username);
			//$username = "admin";
			$query3 = "SELECT * FROM users WHERE username='$username' OR device_code = '$device_code'";
			$result3 = mysqli_query($db, $query3);

			if (mysqli_num_rows($result3) >= 1){
				array_push($errors, "User already Registered");
			}
			$password = md5("12345");//encrypt the password before saving in the database
		

		// register user if there are no errors in the form
		if (count($errors) == 0) {
			
			$query = "INSERT INTO users (username,name,mobile_number,meter_number,device_code, location,email,national_id,role, password) 
					  VALUES('$username','$name','$mobile_number','$meter_number','$device_code','$location', '$email','$national_id','user','$password')";
			if(mysqli_query($db, $query)){

			array_push($user_add_successes, "User Added Successfully");
			
			$query4 = "INSERT INTO user_account (username,balance,mood,loan, switch_1,switch_2) 
					  VALUES('$username',0,'under construction','1','1','1')";
			mysqli_query($db, $query4);
			$query5 = "INSERT INTO total_unit (username,unit_minute) 
					  VALUES('$username',0)";
			mysqli_query($db, $query5);
			

			$_SESSION['username'] = $username;
			$_SESSION['success'] = "You are now logged in";
			// header('location: add_user.php');

			}
		}

	}

	// ... 

	// LOGIN USER
	if (isset($_POST['login'])) {
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$password = mysqli_real_escape_string($db, $_POST['password']);

		if (empty($username)) {
			array_push($errors, "Username is required");
		}
		if (empty($password)) {
			array_push($errors, "Password is required");
		}

		if (count($errors) == 0) {

			$password = md5($password);
			$query6 = "SELECT * FROM users WHERE username='$username' AND password='$password'";
			$result6 = mysqli_query($db, $query6);

			if (mysqli_num_rows($result6) == 1) {
				$_SESSION['username'] = $username;

				$query7 = "SELECT role FROM users WHERE username='$username'";

				$result7 = mysqli_query($db, $query7);

				while($row = mysqli_fetch_assoc($result7)) {
				    $_SESSION['role'] = $row['role'];
				  } 

				$_SESSION['success'] = "You are now logged in";
				
				if($_SESSION['role'] == 'admin'){
					header('location: adindex.php');
				}else{
					header('location: index.php');
				}	
			}else {
				array_push($errors, "Wrong Mobile Number/password combination");
			}
		}
	}
	
	//Add Location By Admin

	if (isset($_POST['add_location'])) {
		// receive all input values from the form
		$location = mysqli_real_escape_string($db, $_POST['location']);


		// form validation: ensure that the form is correctly filled
		if (empty($location)) { array_push($add_location_errors, "Location is required"); }

		$location = strtolower($location);


		$query12 = "SELECT * FROM location WHERE location='$location'";
		$result12 = mysqli_query($db, $query12);

		if (mysqli_num_rows($result12) >= 1){
			array_push($add_location_errors, "Location already inserted");
		}

		// register user if there are no errors in the form
		if (count($add_location_errors) == 0) {
			
			$query13 = "INSERT INTO location (location) 
					  VALUES('$location')";
			
			if(mysqli_query($db, $query13)){
				array_push($add_location_success, "Location ".$location." added successfully ");
			}
		}
	}


	//Payment add by User

	if (isset($_POST['user_add_payment'])) {
		// receive all input values from the form
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$mobile_number = mysqli_real_escape_string($db, $_POST['bkash_number']);
		$amount = mysqli_real_escape_string($db, $_POST['amount']);
		$recipt = mysqli_real_escape_string($db, $_POST['trx_id']);


		// form validation: ensure that the form is correctly filled
		if (empty($mobile_number)) { array_push($errors, "Bkash Number is required"); }
		if (empty($amount)) { array_push($errors, "Amount is required"); }
		if (empty($recipt)) { array_push($errors, "Transaction ID is required"); }


		$query8 = "SELECT * FROM payment WHERE recipt='$recipt'";
		$result8 = mysqli_query($db, $query8);

		if (mysqli_num_rows($result8) >= 1){
			array_push($errors, "Transaction ID already inserted");
		}

		$amount = (double)$amount;
		if ($amount < 300){
			array_push($errors, "You have to recharge minimum 300tk");
		}

		// register user if there are no errors in the form
		if (count($errors) == 0) {
			
			$query9 = "INSERT INTO payment (username,amount,recipt,p_date,month,year,mobile_number,type,status,show_data) 
					  VALUES('$username','$amount','$recipt','$current_date','$current_month', '$current_year','$mobile_number','bkash','pending','1')";
			mysqli_query($db, $query9);

			
				header('location: user_billing.php');
		}
	}

	//Payment Add By Admin

	if (isset($_POST['add_payment'])) {
		// receive all input values from the form
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$amount = mysqli_real_escape_string($db, $_POST['amount']);
		$recipt = mysqli_real_escape_string($db, $_POST['money_recipt']);

		$username = strtolower($username);
		$amount = strtolower($amount);
		$recipt = strtolower($recipt);


		// form validation: ensure that the form is correctly filled
		if (empty($username)) { array_push($payment_add_errors, "Username is required"); }
		if (empty($amount)) { array_push($payment_add_errors, "Amount is required"); }
		if (empty($recipt)) { array_push($payment_add_errors, "Recipt No is required"); }


		$query8a = "SELECT * FROM payment WHERE recipt='$recipt'";
		$result8a = mysqli_query($db, $query8a);

		if (mysqli_num_rows($result8a) >= 1){
			array_push($payment_add_errors, "Recipt ID already inserted");
		}

		$amount = (double)$amount;
		if ($amount < 300){
			array_push($payment_add_errors, "You have to recharge minimum 300tk");
		}

		// register user if there are no errors in the form
		if (count($payment_add_errors) == 0) {

			$demand_charge = 15;
			$meter_rent = 40;
			$service_charge = 10;
			$percentage = ($amount * 5)/100;
 			

			$query10a = "SELECT * FROM payment WHERE username ='$username' AND year = '$current_year' AND month = '$current_month' AND status = 'accepted'";
        	$result10a = mysqli_query($db, $query10a);

        	if(mysqli_num_rows($result10a) >= 1){
                $amount = $amount - $percentage;
        	}
        	else{
        	    $amount = $amount - ($percentage + $demand_charge + $service_charge + $meter_rent);
        	}

        	$query9a = "INSERT INTO payment (username,amount,recipt,p_date,month,year,mobile_number,type,status,show_data) 
					  VALUES('$username','$amount','$recipt','$current_date','$current_month', '$current_year','n/a','cash','accepted','1')";

			if(mysqli_query($db, $query9a)){
				$query11a = "SELECT * FROM user_account WHERE username ='$username'";
        			$result11a = mysqli_query($db, $query11a);
        			while($row = mysqli_fetch_assoc($result11a)) {
                		$balance = (double)$row['balance'];
                		$loan_status = $row['loan'];
        	} 
            
        	if($loan_status == '0'){
                $amount = $amount - 50;
                $query12a = " UPDATE user_account SET loan = '1' WHERE username = '$username'";
                mysqli_query($db, $query12a);
        	}

        	$balance = $balance + $amount;
        	$query13a = " UPDATE user_account SET balance = '$balance' WHERE username = '$username'";
        	mysqli_query($db, $query13a);

			array_push($payment_add_successes, "Payment added successfully ");
			}
		}
	}


	//Add notification by Admin
	if (isset($_POST['add_notification'])) {
		// receive all input values from the form
		$date_for = mysqli_real_escape_string($db, $_POST['date']);
		$subject = mysqli_real_escape_string($db, $_POST['subject']);
		$message = mysqli_real_escape_string($db, $_POST['message']);
		


		// form validation: ensure that the form is correctly filled
		if (empty($date_for)) { array_push($errors, "Date is required"); }
		if (empty($subject)) { array_push($errors, "Subject is required"); }
		if (empty($message)) { array_push($errors, "Message is required"); }


		if ($date_for < $current_date) {
			array_push($notification_errors, "You can't use previous date");
		}

		// register user if there are no errors in the form
		if (count($notification_errors) == 0) {
			

			$query10 = "INSERT INTO notification (date_for,subject,message, posted_date) 
					  VALUES('$date_for','$subject','$message','$current_date')";
			
			mysqli_query($db, $query10);
			
			header('location: announcement.php');
		}
	}

	//Update User By Admin
	if (isset($_POST['update'])) {
		// receive all input values from the form
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$name = mysqli_real_escape_string($db, $_POST['name']);
		$mobile_number = mysqli_real_escape_string($db, $_POST['mobile_number']);
		$national_id = mysqli_real_escape_string($db, $_POST['national_id']);
		$email = mysqli_real_escape_string($db, $_POST['email']);


		// form validation: ensure that the form is correctly filled
		if (empty($username)) { array_push($errors, "Username is required"); }
		if (empty($name)) { array_push($errors, "Name is required"); }
		if (empty($mobile_number)) { array_push($errors, "Mobile Number is required"); }
		if (empty($national_id)) { array_push($errors, "NID is required"); }
		if (empty($email)) { array_push($errors, "Email is required"); }



		// register user if there are no errors in the form
		if (count($errors) == 0) {
			
			$query11 = " UPDATE users SET 
			name = '$name',
			mobile_number = '$mobile_number',
			national_id = '$national_id',
			email = '$email'
			WHERE username = '$username'";
			if(mysqli_query($db, $query11)){
				header('location: user_list.php');
			}
		}
	}

	//Change Password

	if (isset($_POST['change_password'])) {
		// receive all input values from the form
		$username = strtolower($_POST['username']);
		$current_password = $_POST['current_password'];
		$new_password_1 = $_POST['new_password_1'];
		$new_password_2 = $_POST['new_password_2'];


		// form validation: ensure that the form is correctly filled
		
		if (empty($current_password)) { array_push($errors, "Current Password is required"); }
		if (empty($new_password_1)) { array_push($errors, "Please Enter New Password"); }
		if (empty($new_password_2)) { array_push($errors, "Please Retype New Password"); }

		if ($new_password_1 != $new_password_2) {
			array_push($errors, "The two passwords do not match");
		}

		$current_password = md5($current_password);
		$new_password = md5($new_password_1);

		$query14 = "SELECT * FROM users WHERE username='$username'";
		$result14 = mysqli_query($db, $query14);

		while($row = mysqli_fetch_assoc($result14)){
			if($row['password'] != $current_password) {
			array_push($errors, "Current Password doesn't match");
		}
		}

		// register user if there are no errors in the form
		if (count($errors) == 0) {

			$query15 = " UPDATE users SET password = '$new_password' WHERE username = '$username'";
        	if(mysqli_query($db, $query15)){
				header('location: index.php');
        	}
		}

				
	}

	// Sent OTP
	if(isset($_POST['sendotp'])) {
		$user = strtolower($_POST['user']);
		if (empty($user)) { array_push($errors, "Username is required"); }

        $query16 = "SELECT * FROM users WHERE username='$user'";
        $result16 = mysqli_query($db, $query16);
		if(mysqli_num_rows($result16) >= 1){
		while($row = mysqli_fetch_assoc($result16)){
         
                $email = $row['email'];
                $name = $row['name'];
        }
    	}
    	else{
                array_push($errors, "Invalid Username");
            }
            if (count($errors) == 0) {
            	ini_set('display_errors',1);
            	error_reporting( E_ALL );
                $otp = mt_rand(10000, 99999);
                $from = "abirdas@abirdas.xyz";
                $to = $email;
                $subject = "OTP for reset password";
                $message = "Dear $name, you requested for a OTP password. Here is your OTP: $otp";
                $headers = "From:" . $from;
                setcookie('otp', $otp);
                if(mail($to, $subject, $message, $headers)){
                    $otp_out = 1;
                }
            }     
    }

    //Verify OTP
    if(isset($_POST['verifyotp'])) { 
        $user = $_POST['user'];
        $otp = $_POST['otp'];
        print_r($otp);
        if( $_COOKIE['otp'] == $otp) {
        $_SESSION['user'] = $user;
            header('location: reset_password.php');
        }
        else {
            array_push($errors, "OTP Doesn't match");
        }
    }

    //Reset Password
	if (isset($_POST['reset_password'])) {
		// receive all input values from the form
		$user = strtolower($_POST['user']);
		$new_password_1 = $_POST['new_password_1'];
		$new_password_2 = $_POST['new_password_2'];


		// form validation: ensure that the form is correctly filled
		
		if (empty($new_password_1)) { array_push($errors, "Please Enter New Password"); }
		if (empty($new_password_2)) { array_push($errors, "Please Retype New Password"); }

		if ($new_password_1 != $new_password_2) {
			array_push($errors, "The two passwords do not match");
		}

		$new_password = md5($new_password_1);


		// register user if there are no errors in the form
		if (count($errors) == 0) {
	
			$query17 = " UPDATE users SET password = '$new_password' WHERE username = '$user'";
			if(mysqli_query($db, $query17)){
				session_destroy();
        		unset($_SESSION['user']);
				$reset_out = 1;
			}

				
		}
	}




?>