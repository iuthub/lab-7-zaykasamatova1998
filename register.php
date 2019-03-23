<?php
include('connection.php');
session_start();

	$user = false;
	$username = "";
	$fullname =	"";
	$email = "";
	$pwd = "";
	$dob = "";
	if(isset($_SESSION["user"]) && $_SESSION["user"]){
		$user = $_SESSION["user"];
		$username = $user["username"];
		$fullname =	$user["fullname"];
		$email = $user["email"];
		$pwd = $user["password"];
		$dob = $user["dob"];
		$user_id = $user["id"];
	}
	$error = false;
	if ($_SERVER["REQUEST_METHOD"]=="POST") {
		$username = isset($_POST["username"]) ? $_POST["username"] : "";
		$fullname = isset($_POST["fullname"]) ? $_POST["fullname"] : "";
		$email = isset($_POST["email"]) ? $_POST["email"] : "";
		$pwd = isset($_POST["pwd"]) ? $_POST["pwd"] : "";
		$confirm_pwd = isset($_POST["confirm_pwd"]) ? $_POST["confirm_pwd"] : "";
		$dob = isset($_POST["dob"]) ? $_POST["dob"] : "";
		if(!$username || !$fullname || !$email || !$dob || !$pwd || !$pwd || ($pwd != $confirm_pwd))
			$error = true;
		if(!$error){
			if($user){
				$user["username"] = $username;
				$user["fullname"] = $fullname;
				$user["email"] = $email;
				$user["dob"] = $dob;
				$user["password"] = $pwd;
				$_SESSION["user"] = $user;
			}
			$connection = new Connection();
			if($user){
				$stmp = $connection->prepare("UPDATE users set username = ?, fullname = ?, email = ?, password = ?, dob = ? where users.id = $user_id");
			}else{
				$stmp = $connection->prepare("INSERT INTO users (username, fullname, email, password, dob)
					VALUES (?, ?, ?, ?, ?)");
			}

			try{
				$stmp->execute([$username, $fullname, $email, $pwd, $dob]);
			} catch(Exception $e){
				echo $e->getMessage();
				exit;
			}

			header("Location: index.php");
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>My Blog - <?php echo $user ? "Profile form" : "Registration Form" ?></title>
		<link href="style.css" type="text/css" rel="stylesheet" />
	</head>

	<body>
		<?php include('header.php'); ?>

		<h1><?= $user ? "Welcome to your profile page <i>".$user["fullname"]."</i>" : "" ?></h1>
		<h2>User Details Form</h2>
		<h4>Please, fill below fields correctly</h4>
		<form action="register.php" method="post">
				<ul class="form">
					<li>
						<label for="username">Username</label>
						<input type="text" name="username" id="username" required value="<?= $username ?>"/>
					</li>
					<li>
						<label for="fullname">Full Name</label>
						<input type="text" name="fullname" id="fullname" required value="<?= $fullname ?>" />
					</li>
					<li>
						<label for="email">Email</label>
						<input type="email" name="email" id="email" value="<?= $email ?>" />
					</li>
					<li>
						<label for="pwd">Password</label>
						<input type="password" name="pwd" id="pwd" required value="<?= $pwd ?>"/>
					</li>
					<li>
						<label for="confirm_pwd">Confirm Password</label>
						<input type="password" name="confirm_pwd" id="confirm_pwd" required value="<?= $pwd ?>" />
					</li>
					<li>
						<label for="dob">Date of birth</label>
						<input type="date" name="dob" id="dob" required value="<?= date("Y-m-d", strtotime($dob)) ?>"/>
					</li>

						<li>
							<input type="submit" value="Submit" /> &nbsp;
							<?php if(!$user): ?>
								Already registered? <a href="index.php">Login</a>
							<?php endif; ?>
						</li>

				</ul>
		</form>
	</body>
</html>
