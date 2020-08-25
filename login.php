<?php
	session_start();
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);


	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;
	$file = isset($_POST["picToUpload"]) ? $_POST["picToUpload"] : false;

	if (isset($_POST["submit"])){
		$uploadFile = $_FILES["picToUpload"];
		if (($uploadFile["type"] == "image/jpeg" || $uploadFile["type"] == "image/jpg") && ($uploadFile["size"] < 1048576)){
			//Making directory if it doesn't exist
			if (!is_dir("gallery"))
				mkdir("gallery");
			//Uploading to directory
			$target_file = "gallery/".basename($uploadFile["name"]);
			pathinfo($target_file, PATHINFO_EXTENSION);
			move_uploaded_file($uploadFile["tmp_name"], $target_file);

			$file = $uploadFile["name"];
			$userID = $_SESSION['user_id'];
			$InsertSQL = "INSERT INTO tbgallery (user_id, filename) VALUES ('$userID','$file')";
			mysqli_query($mysqli, $InsertSQL);
		}
	}
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Name Surname">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					$_SESSION['user_id'] = $row['user_id'];
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";

					
					
				
					echo 	"<form action='login.php' method='post' enctype	='multipart/form-data'>
								<div class='form-group'>
									<input type='hidden' id='loginEmail' class='form-control' name='loginEmail' value='".$email."'>
									<input type='hidden' id='loginPass' class='form-control' name='loginPass' value='".$pass."'>								
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
								</div>
						  	</form>";
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
				if (isset($_POST["loginEmail"])){
					$UID = $_SESSION["user_id"];
					$SelectPics = "SELECT * FROM tbgallery WHERE user_id= '$UID'";
					$result = $mysqli->query($SelectPics);
					$Total =0;
					$Pictures = array();
					if ($result->num_rows > 0){
						while ($row = $result->fetch_assoc()){
							$Pictures[$Total] = $row["filename"];
							$Total++;
						}
						echo "<h1>Image Gallery</h1>
							<div class='row imageGallery'>";
						for ($i = 0; $i < $Total ; $i++){
							echo '<div class="col-3" style="background-image: url(gallery/'.$Pictures[$i].')"></div>';
						}
						echo "</div>";
					}
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>