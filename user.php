<?php
	session_start();
	include "Hybrid_CRUD.php";
	$hybrid = new Hybrid_CRUD();

	if(!isset($_SESSION['USERNAME'])){
		header("location:index.php");
	}

	if(isset($_POST['logout'])){
		$hybrid->logout();
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>	

	<table class="table">
				<tr>
					<th style="font-size:20pt;">Welcome <?php echo $_SESSION['FNAME'] . " " . $_SESSION['LNAME']; ?></th>
					<td>
						<button name = "logout" class="btn btn-dark" style="float:right;">Logout</button>
					</td>
				</tr>
			</table>

</body>
</html>