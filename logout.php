<?php
session_start();
session_unset(); // uklanja sve sesijske promenljive
session_destroy(); // uništava sesiju

// Redirekcija na login stranicu
header("Location: login.php");
exit();
?>
