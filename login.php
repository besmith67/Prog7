<?php
// Start or resume session, and create: $_SESSION[] array
session_start(); 
require '../database/database.php';
if ( !empty($_POST)) { // if $_POST filled then process the form
	// initialize $_POST variables
	$username = $_POST['username']; // username is email address
	$password = $_POST['password'];
	$passwordhash = MD5($password);
	$labelError = "";
		
	// verify the username/password
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM coach WHERE email = ? AND password_hash = ? LIMIT 1";
	$q = $pdo->prepare($sql);
	$q->execute(array($username,$passwordhash));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	
	if($data) { // if successful login set session variables
		$_SESSION['coachid'] = $data['coach_id'];
		$sessionid = $data['coach_id'];
		Database::disconnect();
		header("Location: player.php"); //Successfully logs in and shows records
		exit();
	}
	else { // display error
		Database::disconnect();
		$labelError = "Incorrect username/password";
	} 
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>       
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>

</head>

<body>
    <div class="container" style="margin-top: 10em;margin-left:40%">

		<div class="span10 offset1">

			<div class="row">
				<h3>Coach Login</h3>
			</div>

			<form class="form-horizontal" action="login.php" method="post">
								  
				<div class="control-group">
					<label class="control-label">Username (Email)</label>
					<div class="controls">
						<input name="username" type="text"  placeholder="me@email.com" required> 
					</div>	
				</div> 
				
				
				<div class="control-group">
					<label class="control-label">Password</label>
					<div class="controls">
						<input name="password" type="password" placeholder="password" required> 
					</div>	
				</div> 
				
								<br />

				<div class="form-actions">
					<button type="submit" class="btn btn-success">Log in</button>
					&nbsp; &nbsp;
					<a class="btn btn-primary" href="join.php">Join</a>
				</div>
				
				<div>
				<?php
					echo "<span class='help-inline'>";
					echo "&nbsp;&nbsp;" . $labelError;
					echo "</span>";
				?>
				</div>
				

				
			</form>


		</div> <!-- end div: class="span10 offset1" -->
				
    </div> <!-- end div: class="container" -->

  </body>
  
</html>