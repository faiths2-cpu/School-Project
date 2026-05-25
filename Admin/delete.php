<?php
include "admin_PDO.php";

$ADMIN = new admin_PDO();

// Check if user_id is set and is numeric
if(isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    $delete = $ADMIN->delete_user($user_id);

    if($delete){
        echo "
            <script>
                alert('User removed successfully.');
                window.location.href = 'index.php';
            </script>
        ";
    } else {
        echo "
            <script>
                alert('Failed to remove user. User may not exist.');
                window.location.href = 'index.php';
            </script>
        ";
    }
} else {
    echo "
        <script>
            alert('Invalid user ID.');
            window.location.href = 'index.php';
        </script>
    ";
}
?>