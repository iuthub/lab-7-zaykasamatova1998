<?php  

include('connection.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>My Blog - Registration Form</title>
		<link href="style.css" type="text/css" rel="stylesheet" />
		<style media="screen">
      		.error {
       			 color: red;
      		}
   		 </style>
	</head>
	
	<body>

		<?php 
		session_start();
		error_reporting(0);
		$pass_error = false;
		$isPost = $_SERVER["REQUEST_METHOD"]=="POST";
		$isGet = $_SERVER["REQUEST_METHOD"]=="GET";
		$username = $_REQUEST["username"];
		$fullname = $_REQUEST["fullname"];
		$change = $_REQUEST["change"];
		$email = $_REQUEST["email"] == ""? null:$_REQUEST["email"];
		$pwd = $_REQUEST["pwd"];
		$cpwd = $_REQUEST["confirm_pwd"];

		$servername = "localhost";
		$dbname = "blog";
		$conn = new mysqli($servername,"root","",$dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
			if(!$isPost){
				include('header.php'); 
			}else{
				if($cpwd == $pwd){
					if(isset($_SESSION["user"])){
						$sql = 'UPDATE users set username = "'.$username.'", password = "'.$pwd.'", email = "'.$email.'", fullname = "'.$fullname.'" WHERE id = "'.$_SESSION["user"]["id"].'"';
						$conn->query($sql);
						header("Location: index.php");
					}else{
						$sql = "INSERT INTO Users (username, email, password, fullname) VALUES ('".$username."', '".$email."', '".$pwd."', '".$fullname."')";
						if (mysqli_query($conn, $sql)){
							header("Location: index.php");
						}
						$conn->close();
					}
						
		
				}else{
					$pass_error = true;
				}
			}
		?>

		<h2>User Details Form</h2>
		<h4>Please, fill below fields correctly</h4>
		<form action="register.php" method="post">
		<?php if($isGet || $pass_error) { ?>
				<ul class="form">
					<li>
						<label for="username">Username</label>
						<input type="text" name="username" id="username" <?php if(isset($_SESSION["user"])){?> value = "<?= $_SESSION["user"]["username"] ?>" <?php } else { ?> value = "<?= $username ?>" <?php } ?> required/>
					</li>
					<li>
						<label for="fullname">Full Name</label>
						<input type="text" name="fullname" id="fullname" <?php if(isset($_SESSION["user"])){?> value = "<?= $_SESSION["user"]["fullname"] ?>" <?php } else { ?> value = "<?= $fullname ?>" <?php } ?> required/>
					</li>
					<li>
						<label for="email">Email</label>
						<input type="email" name="email" id="email" <?php if(isset($_SESSION["user"])){?> value = "<?= $_SESSION["user"]["email"] ?>" <?php } else { ?> value = "<?= $email ?>" <?php } ?> />
					</li>
					<li>
						<label for="pwd">Password</label>
						<input type="password" name="pwd" id="pwd" required/>
					</li>
					<li>
						<label for="confirm_pwd">Confirm Password</label>
						<input type="password" name="confirm_pwd" id="confirm_pwd" required />
						<span class = "error"><?= $pass_error?"Passwords do not match!":"" ?></span>
					</li>
					<li>
						<input type="submit" value="Submit" /> &nbsp; Already registered? <a href="index.php">Login</a>
					</li>
				</ul>
				<?php }?>
		</form>
	</body>
</html>