<html>
	<head>
		<title>Registration Form</title>
		<style>
			.container{
				width: 60%;
				margin: 0px auto;
				padding: 20px;
				background-color: #efefef;
			}
			.formgroup{
				padding: 10px 0px;
			}
			.formcontrol{
				width: 90%;
				height: 36px;
				border-radius: 7px;
				border: 4px solid #985;
			}
			.btn{
				background-color: blue;
				padding: 10px 20px;
				border: none;
				color: #fff;
				border-radius: 6px;
			}
			.error{
				color: #f00;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<h1>Registration Form</h1>
			<?php 
				if(isset($_COOKIE['success']))
				{
					echo "<p>".$_COOKIE['success']."</p>";
				}
				if(isset($_COOKIE['error']))
				{
					echo "<p>".$_COOKIE['error']."</p>";
				}
					
				function filterData($data)
				{
					return addslashes(strip_tags(trim($data)));
				}
				
				if(isset($_POST['Register']))
				{
					$uname = isset($_POST['uname']) ? filterData($_POST['uname']) : '';
					$mail = isset($_POST['email']) ? filterData($_POST['email']) : '';
					$pass = isset($_POST['pwd']) ? filterData($_POST['pwd']) : '';
					$cpass = isset($_POST['cpwd']) ? filterData($_POST['cpwd']) : '';
					$mobile = isset($_POST['mobile']) ? filterData($_POST['mobile']) : '';
					$gender = isset($_POST['gender']) ? filterData($_POST['gender']) : '';
					$state = isset($_POST['state']) ? filterData($_POST['state']) : '';
					$tnc = isset($_POST['terms']) ? filterData($_POST['terms']) : '';
					
					$ip = $_SERVER['REMOTE_ADDR'];
					date_default_timezone_set("Asia/Kolkata");
					$date = date("Y-m-d h:i:s");
					$token = md5(str_shuffle($uname.$mail.$state.time()));
					$hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
					
					// vaidations
					$errors = [];
					
					// username validation
					if($uname === "")
					{
						$errors["uname"] = "Username is required";
					}
					else
					{
						if(strlen($uname) <= 3){
							$errors["uname"] = "Username should contains atleaset 4 characters";
						}
					}
					
					// email validation
					if($mail === "")
					{
						$errors['email'] = "Email is Required";
					}
					else
					{
						if(!filter_var($mail, FILTER_VALIDATE_EMAIL))
						{
							$errors['email'] = "Valid email id is Required";
						}
					}
					
					//password validation
					if($pass === "")
					{
						$errors["pass"] = "Password is Required";
					}
					else
					{
						$pattern = '/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/';
						if(!(preg_match($pattern, $pass))){
							$errors['pass'] = "Password must include one uppercase letter, one lowercase letter, one number, and one special character such as $ or %.";
						}
					}
					
					// confirm password vaidation
					if($cpass === "")
					{
						$errors['cpass'] = "Confirm password is Required";
					}
					if($pass !== $cpass)
					{
						$errors['cpass'] = "Password and confirm password does not matched";
					}
					
					// Mobile validation
					if($mobile === "")
					{
						$errors["mobile"]= "Mobile is Required";
					}
					else
					{
						if(preg_match('/^[0-9]{10}+$/', $mobile)) 
						{
							$mobile;
						} 
						else 
						{
							$errors['mobile'] = "Invalid Mobile Number";
						}
					}
					
					// gender validation
					if($gender === "")
					{
						$errors["gender"]="Gender is Required";
					}
					
					// validate the state
					if($state === "")
					{
						$errors['state'] = "State is Required";
					}
					
					// tnc validation
					if($tnc === "")
					{
						$errors['terms'] = "Please Accept Terms and Conditions";
					}
					
					if(count($errors) === 0)
					{
						include ('practices.php');
						mysqli_query($connect,"INSERT INTO registerdata(username,email,password,mobile,gender,state,terms,created_at,token,ip) VALUES('$uname','$mail','$hashed_pass','$mobile','$gender','$state','$tnc','$date','$token','$ip')");
						
						if(mysqli_affected_rows($connect) === 1)
						{
							$to      = $mail;
							$subject = 'Account Activation link - Kanexy';
							$message = 'Hi '.$uname."<br />Thanks your account has created successfully. Please click the below link to Activate your account.<br /><br /><a target='_blank' href='http://localhost/programs/MySQL/activation_link.php?token=$token'>Activate</a> <br><br>Thanks<br/>Team";
							$headers = 'From: webmaster@example.com' . "\r\n" .
								'Reply-To: webmaster@example.com' . "\r\n" .
								'Content-Type: text/html' . "\r\n" .
								'X-Mailer: PHP/' . phpversion();
							//echo $message;
							//exit;
							
							if(mail($to, $subject, $message, $headers))
							{
								setcookie("success", "Account created successfully, Please check your registered email id for activation link", time() + 6);
								header("Location: registration_form.php");
							}
							else
							{
								setcookie("error", "Account Created successfully! Unable to send activation link, contact admin", time() + 6);
								header("Location: registration_form.php");
							}
						}
						else
						{
							setcookie("error", "Sorry! Unable to create an account. ".mysqli_error($connect), time() + 6);
							header("Location: registration_form.php");
						}
					}
				}
			?>
			<form method="POST" action="">
				<div class="formgroup">
					<label>Username</label>
					<input type="text" name="uname" value="<?php if(isset($_POST['uname'])) echo $_POST['uname']; ?>" class="formcontrol" />
					<small class="error"><?php if(isset($errors['uname'])) echo $errors['uname']; ?></small>
				</div>
				<div class="formgroup">
					<label>Email</label>
					<input type="text" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" class="formcontrol" />
					<small class="error"><?php if(isset($errors['email'])) echo $errors['email']; ?></small>
				</div>
				<div class="formgroup">
					<label>Password</label>
					<input type="password" name="pwd" value="<?php if(isset($_POST['pwd'])) echo $_POST['pwd']; ?>" class="formcontrol" />
					<small class="error"><?php if(isset($errors['pass'])) echo $errors['pass']; ?></small>
				</div>
				<div class="formgroup">
					<label>Confirm Password</label>
					<input type="password" name="cpwd" value="<?php if(isset($_POST['cpwd'])) echo $_POST['cpwd']; ?>" class="formcontrol" />
					<small class="error"><?php if(isset($errors['cpass'])) echo $errors['cpass']; ?></small>
				</div>
				<div class="formgroup">
					<label>Mobile</label>
					<input type="text" name="mobile" value="<?php if(isset($_POST['mobile'])) echo $_POST['mobile']; ?>" class="formcontrol" />
					<small class="error"><?php if(isset($errors['mobile'])) echo $errors['mobile']; ?></small>
				</div>
				<div class="formgroup">
					<label>Gender</label>
					<label>
						<input type="radio" <?php if(isset($_POST['gender'])) if($_POST['gender'] === "Male") echo "checked"; ?> name="gender" value="Male" />Male
					</label>
					<label>
						<input type="radio" <?php if(isset($_POST['gender'])) if($_POST['gender'] === "Female") echo "checked"; ?> name="gender" value="Female" />Female
					</label>
					<small class="error"><?php if(isset($errors['gender'])) echo $errors['gender']; ?></small>
				</div>
				<div class="formgroup">
					<label>State</label>
					<select class="formcontrol" name="state">
						<option value="">--Select State--</option>
						<option <?php if(isset($_POST['state'])) if($_POST['state'] === "Andhrapradesh") echo "Selected"; ?> value="Andhrapradesh">Andhrapradesh</option>
						<option <?php if(isset($_POST['state'])) if($_POST['state'] === "Telangana") echo "Selected"; ?> value="Telangana">Telangana</option>
						<option <?php if(isset($_POST['state'])) if($_POST['state'] === "Telangana") echo "Selected"; ?> value="Karnataka">Karnataka</option>
						<option <?php if(isset($_POST['state'])) if($_POST['state'] === "Telangana") echo "Selected"; ?> value="Goa">Goa</option>
						<option <?php if(isset($_POST['state'])) if($_POST['state'] === "Telangana") echo "Selected"; ?> value="Kerala">Kerala</option>
						<option <?php if(isset($_POST['state'])) if($_POST['state'] === "Telangana") echo "Selected"; ?> value="Maharashtra">Maharashtra</option>
					</select>
					<small class="error"><?php if(isset($errors['state'])) echo $errors['state']; ?></small>
				</div>
				<div class="formgroup">
					<label>
						<input <?php if(isset($_POST['terms'])) if($_POST['terms'] === "yes") echo "checked";  ?> type="checkbox" value="yes" name="terms" /> Please accept terms and conditions
					</label>
					<small class="error"><?php if(isset($errors['terms'])) echo $errors['terms']; ?></small>
				</div>
				<div class="formgroup">
					<input type="Submit" name="Register" value="Register" class="btn" />
				</div>
			</form>
		</div>
	</body>
</html>