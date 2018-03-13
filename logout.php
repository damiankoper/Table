<?php 
unset($_SESSION['admin_session']);
session_destroy();
header("Location: login.php");
?>