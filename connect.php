<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$db = "jobportal";

	try {
		// Create connection
		$con = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
	} catch(PDOException $e) {
		echo "Error: " . $e->getMessage() . "<br>";
		die();
	}
?>