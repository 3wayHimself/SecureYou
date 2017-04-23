<?php
/**
 *  SecureYou 1.0 - PHP Library For Login/Register
 *
 *  @author Alemalakra
 *  @version 1.0
 */


// Library

require('src/secureyou.php');


// Database Connection

try {
	$DBHost = 'localhost';
	$DBName = '';
	$DBUser = '';
	$DBPass = '';
	$_cmd = 'mysql:host=' . $DBHost . ';dbname=' . $DBName;
    $database = new PDO($_cmd, $DBUser, $DBPass);
} catch (PDOException $e) {
    die("¡Error!: " . $e->getMessage() . "<br/>");
}


// Class Configuration

$Username = "Alemalakra";
$Email = "Alemalakra@gmail.com";
$Password = 'Alemalakra000';
$SecureYou = new SecureYou($database);
$TypeOfAuth = 'Register'; // You Can Use Login



// Handler

if ($SecureYou->isLogged() == False) {
	switch ($TypeOfAuth) {
		case 'Register':
			if ($TypeOfAuth == 'Register') {
				if ($SecureYou->userexist($Username, $Email) == false) {
					if ($SecureYou->register($Username, $Email, $Password) == true) {
						echo 'Registred With User: ' . $Username;
					} else {
						echo 'Error Registring: ' . $SecureYou->getMessage();
					}
				} else {
					echo 'Username Alredy Exists!';
				}
			}
			break;
		case 'Login':
			if ($SecureYou->userexist($Username, $Email) == true) {
				if ($SecureYou->login($Username, $Password) == true) {
					echo 'Logged Now With Username: ' . $Username;
				} else {
					echo 'Error On Login: ' . $SecureYou->getMessage();
				}
			} else {
				echo 'Username no Exist!';
			}
			break;

	}
} else {
	echo "Logged: True <br>Username: " . $_SESSION['username'] . " <br> ID: " . $_SESSION['userid'] . ' <br> Email: ' . $_SESSION['useremail'];
}
?>
