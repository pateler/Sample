<?php session_start(); ?>
<html>
	<head>
		<title>Login Form</title>
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
				width: 100%;
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
			<h1>Login Here</h1>
			<?php 
				function filterData($data)
				{
					return addslashes(strip_tags(trim($data)));
				}
				
				if(isset($_POST['Login']))
				{
					$mail = isset($_POST['email']) ? filterData($_POST['email']) : '';
					$pass = isset($_POST['pwd']) ? filterData($_POST['pwd']) : '';
					
					// vaidations
					$errors = [];
					
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
					
					if(count($errors) === 0)
					{
						include ('practices.php');
						$result = mysqli_query($connect, "select * from registerdata where email='$mail'");
						if(mysqli_num_rows($result) === 1)
						{
							$row = mysqli_fetch_assoc($result);
							if(password_verify($pass, $row['password']))
							{
								if($row['status'] === "active")
								{
									$_SESSION['login_true'] = $row['token'];
									header("Location: home_page.php");
								}
								else
								{
									echo "<p>Please activate your account</p>";
								}
							}
							else
							{
								echo "<p>Password does not matched.</p>";
							}
						}
						else
						{
							echo "<p>Sorry! Unable to find your email account</p>";
						}
					}
					
				}
			?>
			<form method="POST" action="">
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
					<input type="Submit" name="Login" value="Login" class="btn" />
				</div>
			</form>
		</div>
	</body>
</html>