<?php
include('connection.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>My Personal Page</title>
		<link href="style.css" type="text/css" rel="stylesheet" />
		<style media="screen">
      		.error {
       			 color: red;
      		}
   		 </style>
	</head>
	
	<body>
		<?php include('header.php'); ?>
		<?php
			session_start();
			$logout=isset($_REQUEST["logout"])?$_REQUEST["logout"] : null;
			$username = isset($_REQUEST["username"])?$_REQUEST["username"] : null;
			$pwd = isset($_REQUEST["pwd"])?$_REQUEST["pwd"] : null;
			$remember = isset($_REQUEST["remember"])?$_REQUEST["remember"] : null;
			$log_success = false;
			$isPost = $_SERVER["REQUEST_METHOD"]=="POST";
			$isGet = $_SERVER["REQUEST_METHOD"]=="GET";
			if($logout == 1){
				session_destroy();
			}

			if(isset($_SESSION["user"]) && $isPost){
				if(isset($_REQUEST["title"]) && isset($_REQUEST["body"])){
					$title = $_REQUEST["title"];
					$body = $_REQUEST["body"];
					$user_id = $_SESSION["user"]["id"];
					$servername = "localhost";
					$dbname = "blog";
					$conn = new mysqli($servername,"root","",$dbname);
					if ($conn->connect_error) {
						die("Connection failed: " . $conn->connect_error);
					} 
					$sql = "INSERT INTO Posts (title, body, userId) VALUES ('".$title."', '".$body."', '".$user_id."')";
					$conn->query($sql);
					$conn->close();
						}
			}

		if($isPost){
			$servername = "localhost";
			$dbname = "blog";
			$conn = new mysqli($servername,"root","",$dbname);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$sql = "SELECT * FROM  Users WHERE username = '".$username."' AND password = '".$pwd."'";
			$result = mysqli_query($conn, $sql);
			if($result->num_rows > 0){
				$log_success = true;
				$row = mysqli_fetch_assoc($result);
				$_SESSION["user"] = $row;
				if($remember){
					setcookie("username", $username,  time()+60*60*24*365);
				}else{
					setcookie("username", $username,  time()-1);
				}
			}
			
			$conn->close();
		}
			
		?>
		<!-- Show this part if user is not signed in yet -->
		<?php
			if(!isset($_SESSION["user"]) || $logout == 1){
				
		?>
		<?php
			if($log_success == false && $isPost){
		?>
		<span class = "error"><h2> Your username or password is incorrect</h2></span>
		<?php
		
	}
		?>
				
		<div class="twocols">
			<form action="index.php" method="post" class="twocols_col">
				<ul class="form">
					<li>
						<label for="username">Username</label>
						<input type="text" name="username" id="username" <?php if(isset($_COOKIE["username"])){ ?> value="<?=$_COOKIE["username"]?>" <?php } ?> />
					</li>
					<li>
						<label for="pwd">Password</label>
						<input type="password" name="pwd" id="pwd" />
					</li>
					<li>
						<label for="remember">Remember Me</label>
						<input type="checkbox" name="remember" id="remember" checked />
					</li>
					<li>
						<input type="submit" value="Submit" /> &nbsp; Not registered? <a href="register.php">Register</a>
					</li>
				</ul>
			</form>
			<div class="twocols_col">
				<h2>About Us</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur libero nostrum consequatur dolor. Nesciunt eos dolorem enim accusantium libero impedit ipsa perspiciatis vel dolore reiciendis ratione quam, non sequi sit! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio nobis vero ullam quae. Repellendus dolores quis tenetur enim distinctio, optio vero, cupiditate commodi eligendi similique laboriosam maxime corporis quasi labore!</p>
			</div>
		</div>
		<?php
			}else{
		?>

	
		<!-- Show this part after user signed in successfully -->
		<div class="logout_panel"><a href="register.php">My Profile</a>&nbsp;|&nbsp;<a href="index.php?logout=1">Log Out</a></div>
		<h2>New Post</h2>
		<form action="index.php" method="post">
			<ul class="form">
				<li>
					<label for="title">Title</label>
					<input type="text" name="title" id="title" required />
				</li>
				<li>
					<label for="body">Body</label>
					<textarea name="body" id="body" cols="30" rows="10" required></textarea>
				</li>
				<li>
					<input type="submit" value="Post" />
				</li>
			</ul>
		</form>
		<div class="onecol">
			<?php 
				$servername = "localhost";
				$dbname = "blog";
				$conn = new mysqli($servername,"root","",$dbname);
				$sql = "SELECT * FROM  Posts";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					while($row = $result->fetch_assoc()){
						$sqlForName = 'SELECT username FROM  users where id="'.$row["userId"].'"';
						$resultForName = mysqli_query($conn, $sqlForName);
						$nameQuery = mysqli_fetch_assoc($resultForName);
						echo '<div class="card">';
						echo '<h2>'.$row["title"].'</h2>';
						echo '<h5>'.$nameQuery["username"].'</h5>';
						echo '<p>'.$row["body"].'</p>';
						echo '</div>';
					}
				}
			$conn->close();
			?>
		</div>

		<?php
			}
		?>

	</body>

</html>