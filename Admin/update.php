<?php
	include "admin_PDO.php";
	$hybrid = new admin_PDO();

	$user_id = $_GET['user_id'];

	if(isset($_POST['update'])){
		$user_id = $_POST['user_id'];
		$username = $_POST['username'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$updated_at = $_POST['updated_at'];

		$update = $hybrid->update_user($user_id, $username, $firstname, $lastname, $updated_at);

		if($update){
			echo "
				<script>
					alert('Updated User Successfully. ');
					window.location.href = 'index.php';
				</script>
			";
		}
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
	<form method ="post">
		<table>
			<?php
                $fetch = $hybrid->show_selected_user($user_id);
                
                if($fetch && $fetch->rowCount() > 0) {
                    $row = $fetch->fetch(PDO::FETCH_ASSOC);
            ?>
					<tr>
						<td>Username</td>
						<td>
							<input type="text" name="username" value = "<?php echo $row['username']; ?>" required>
						</td>
					</tr>
					<tr>
						<td>First Name</td>
						<td>
							<input type="text" name="firstname" value = "<?php echo $row['firstname']; ?>" required>
						</td>
					</tr>
					<tr>
						<td>Last Name</td>
						<td>
							<input type="text" name="lastname" value = "<?php echo $row['lastname']; ?>" required>
						</td>
					</tr>
					<tr>
						<td>Updated On</td>
						<td>
							<input type="datetime-local" name="updated_at" value = "<?php echo $row['updated_at']; ?>" required>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<button name = "update">Update User</button>
						</td>
					</tr>

					<input type="hidden" name = "user_id" value = "<?php echo $row['user_id']; ?>">
			<?php
				}
			?>
		</table>
	</form>
</body>
</html>