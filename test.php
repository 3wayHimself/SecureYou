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

$Encryptation = '0123456789abcdefghijklmñnop000A1eMalakraBestDev';
$Username = "TestUser";
$Email = "TestUser@gmail.com";
$Password = 'TestUser123';
$SecureYou = new SecureYou($database, $Encryptation);
$TypeOfAuth = 'Register'; // You Can Use Login



// Handler
if ($SecureYou->isLogged() == False) {
	if ($TypeOfAuth == 'Register') {
		if ($SecureYou->userexist($Username, $Email) == false) {
			$SecureYou->register($Username, $Email, $Password); // Session Maked And All Done
			echo 'Registred With User: ' . $Username;
		} else {
			echo 'Username Alredy Exists!';
		}
	} elseif ($TypeOfAuth == 'Login') {
		if ($SecureYou->userexist($Username, $Email) == true) {
			if ($SecureYou->login($Username, $Password) == true) {
				echo 'Logged Now With Username: ' . $UserName;
			} else {
				echo 'Wrong Password!';
			}
		} else {
			echo 'Username no Exist!';
		}
	}
} else {
	echo "Logged: True <br>Username: " . $_SESSION['user'];
}


?>
