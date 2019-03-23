<?php
include('connection.php');
session_start();
$connection = new Connection();
	if ($_SERVER["REQUEST_METHOD"]=="POST") {
		if(isset($_GET["newpost"]) && $_GET["newpost"] && isset($_SESSION["user"]) && $_SESSION["user"]){
			$title = $_POST["title"] ?? "";
			$body = $_POST["body"] ?? "";
			$userId = $_SESSION["user"]["id"];
			$publishDate = date("Y-m-d");
			if($title && $body){
				$title = htmlspecialchars($title);
				$body = htmlspecialchars($body);
				$stmp = $connection->prepare("INSERT INTO posts (title, body, userId, publishDate) VALUES (?, ?, ?, ?)");
				try{
					$stmp->execute([$title, $body, $userId, $publishDate]);
				} catch(Exception $e){
					echo $e->getMessage();
					exit;
				}
				header("Location: index.php");
			}
		}else if(isset($_GET["login"]) && $_GET["login"]){
			$username = isset($_POST["username"]) ? $_POST["username"] : "";
			$pwd = isset($_POST["pwd"]) ? $_POST["pwd"] : "";
			$remember = isset($_POST["remember"]) ? $_POST["remember"] : false;
			$login = false;
			if($username && $pwd){

				$stmp = $connection->prepare("SELECT * from users where username = ?");
				try{
					$stmp->execute([$username]);
					$row = $stmp->fetch();
					if($row && isset($row["username"])){
						if($row["password"] == $pwd){
							$_SESSION["user"] = $row;
							if($remember){
								setcookie("username", $username, time() + 60*60*24*365);
							}else
								setcookie("username", $username, time()-1);
							header("Location: index.php");
						}
					}
				} catch(Exception $e){
					echo $e->getMessage();
					exit;
				}
			}
		}
	}
	if(isset($_GET["logout"]) && $_GET["logout"]){
		session_destroy();
		header("Location: index.php");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>My Personal Page</title>
		<link href="style.css" type="text/css" rel="stylesheet" />
	</head>

	<body>
		<?php include('header.php'); ?>
		<!-- Show this part if user is not signed in yet -->
		<div class="twocols">

			<?php if(!isset($_SESSION["user"]) || !$_SESSION["user"]): ?>
				<form action="index.php?login=1" method="post" class="twocols_col">
					<?php if(isset($login) && !$login): ?>

						<h2>Incorrect data</h2>
					<?php endif; ?>
					<ul class="form">
						<li>
							<label for="username">Username</label>
							<input type="text" name="username" id="username" value="<?= isset($_COOKIE["username"]) ? $_COOKIE["username"] : "" ?>"/>
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
			<?php endif; ?>
			<div class="twocols_col">
				<h2>About Us</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur libero nostrum consequatur dolor. Nesciunt eos dolorem enim accusantium libero impedit ipsa perspiciatis vel dolore reiciendis ratione quam, non sequi sit! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio nobis vero ullam quae. Repellendus dolores quis tenetur enim distinctio, optio vero, cupiditate commodi eligendi similique laboriosam maxime corporis quasi labore!</p>
			</div>
		</div>


		<?php if(isset($_SESSION["user"]) && $_SESSION["user"]): ?>
			<!-- Show this part after user signed in successfully -->
			<div class="logout_panel"><a href="register.php">My Profile</a>&nbsp;|&nbsp;<a href="index.php?logout=1">Log Out</a></div>
			<h2>New Post</h2>
			<form action="index.php?newpost=1" method="post">
				<ul class="form">
					<li>
						<label for="title">Title</label>
						<input type="text" name="title" id="title" />
					</li>
					<li>
						<label for="body">Body</label>
						<textarea name="body" id="body" cols="30" rows="10"></textarea>
					</li>
					<li>
						<input type="submit" value="Post" />
					</li>
				</ul>
			</form>
		<?php endif; ?>
		<div class="onecol">

			<?php
				$stmp = $connection->prepare("SELECT * from posts JOIN users on posts.userId = users.id");
				$posts = [];
				try{
					$stmp->execute();

					while($row = $stmp->fetch()){
						$posts[] = $row;
					}
				} catch(Exception $e){
					echo $e->getMessage();
					exit;
				}
			 ?>

			<?php foreach($posts as $post){ ?>

				<div class="card">
					<h2><?= htmlspecialchars_decode($post["title"]) ?></h2>
					<h5><?php echo $post["fullname"]?>, <?php echo date("M j, Y", strtotime($post["publishDate"])) ?></h5>
					<p><?php echo htmlspecialchars_decode($post["body"]) ?></p>
				</div>

			<?php } ?>
		</div>
	</body>
</html>
