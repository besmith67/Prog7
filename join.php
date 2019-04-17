<?php 

session_start();
	
require '../database/database.php';
if ( !empty($_POST)) { // if not first time through
	// initialize user input validation variables
	$nameError = null;
	$emailError = null;
	$passwordError = null;

	// initialize $_POST variables
	$coach_name = $_POST['name'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$passwordhash = MD5($password);

	// validate user input
	$valid = true;
	if (empty($coach_name)) {
		$nameError = 'Please enter Name';
		$valid = false;
	}
	if (empty($email)) {
		$emailError = 'Please enter valid Email Address (REQUIRED)';
		$valid = false;
	} else if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
		$emailError = 'Please enter a valid Email Address';
		$valid = false;
	}
	//Check if email already exists
	$pdo = Database::connect();
	$sql = "SELECT * FROM coach";
	foreach($pdo->query($sql) as $row) {
		if($email == $row['email']) {
			$emailError = 'Email has already been registered!';
			$valid = false;
		}
	}
	Database::disconnect();
	
	if (empty($password)) {
		$passwordError = 'Please enter Password';
		$valid = false;
	}

	// insert data into database
	if ($valid) 
	{
		$pdo = Database::connect();
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO coach (coach_name,email,password_hash) values(?, ?, ?)";
		$q = $pdo->prepare($sql);
		$q->execute(array($coach_name,$email,$passwordhash)); 
			
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM coach WHERE email = ? AND password_hash = ? LIMIT 1";
		$q = $pdo->prepare($sql);
		$q->execute(array($email,$passwordhash));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		
		$_SESSION['coachid'] = $data['coach_id'];
		
		Database::disconnect();
		header("Location: player.php"); //auto sign in and display list after joining
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
				<h3>Add new Coach</h3>
			</div>
	
			<form class="form-horizontal" action="join.php" method="post">

				<div class="control-group <?php echo !empty($nameError)?'error':'';?>">
					<label class="control-label">Name</label>
					<div class="controls">
						<input name="name" type="text"  placeholder="Name" value="<?php echo !empty($coach_name)?$coach_name:'';?>">
						<?php if (!empty($nameError)): ?>
							<span class="help-inline"><?php echo $nameError;?></span>
						<?php endif; ?>
					</div>
				</div>
				
				
				<div class="control-group <?php echo !empty($emailError)?'error':'';?>">
					<label class="control-label">Email</label>
					<div class="controls">
						<input name="email" type="text" placeholder="Email Address" value="<?php echo !empty($email)?$email:'';?>">
						<?php if (!empty($emailError)): ?>
							<span class="help-inline"><?php echo $emailError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				
				<div class="control-group <?php echo !empty($passwordError)?'error':'';?>">
					<label class="control-label">Password</label>
					<div class="controls">
						<input id="password" name="password" type="password"  placeholder="password" value="<?php echo !empty($password)?$password:'';?>">
						<?php if (!empty($passwordError)): ?>
							<span class="help-inline"><?php echo $passwordError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				<br />
			  
				<div class="form-actions">
					<button type="submit" class="btn btn-success">Confirm</button>
					<a class="btn btn-secondary" href="login.php">Back</a>
				</div>
				
			</form>
			
		</div> <!-- end div: class="span10 offset1" -->
				
    </div> <!-- end div: class="container" -->
  </body>
</html>