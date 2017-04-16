<?php
/**
 *  SecureYou 1.0 - PHP Library For Login/Register
 *
 *  @author Alemalakra
 *  @version 1.0
 */

class SecureYou {
	function __construct($conn, $key) {
		$this->db = $conn;
		$this->key = $key;
	}
	function isLogged() {
		@session_start();
		if (isset($_SESSION['username'])) {
			return true;
		} else {
			return false;
		}
	}
	function register($user, $email, $password) {
		$stmt = $this->db->prepare("INSERT INTO users (email, username, password)VALUES (:email, :username, :password)");
		$stmt -> execute(array(':email' => $email, ':username' => $user, ':password' => $this->encrypt($password)));
		$_SESSION['username'] = $this->encrypt($user);
	}
	function login($user, $password) {
	    $stmt = $this->db->prepare("SELECT username FROM users WHERE username = :name");
	    $stmt->bindParam(':name', $user);
	    $stmt->execute();

	    if($stmt->rowCount() > 0){
	        
		    $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :name");
		    $stmt->bindParam(':name', $user);
		    $stmt->execute();
		    $result = $stmt->fetchAll();
		    if ($password == $this->decrypt($result[0]['password'])) {
		    	$_SESSION['username'] = $this->encrypt($result[0]['username']);
		    	$_SESSION['user'] = $result[0]['username'];
		    	$this->Username = $user;
		    	return true;
		    } else {
		    	return false;
		    }


	    } else {
	        return false;
	    }

	}
	function userexist($user, $email) {
	    
	    $stmt = $this->db->prepare("SELECT username FROM users WHERE username = :name");
	    $stmt->bindParam(':name', $user);
	    $stmt->execute();

	    if($stmt->rowCount() > 0){
	        $testone = true;
	        return true;
	    } else {
	        $testone = false;
	    }
	    if ($testone == true) {
	    	return true;
	    } else {
		    $stmt = $this->db->prepare("SELECT username FROM users WHERE email = :email");
		    $stmt->bindParam(':email', $email);
		    $stmt->execute();
			if($stmt->rowCount() > 0){
				return true;
			} else {
				return false;
			}
	    }
	}
	function encrypt($string) {
		$result = '';
		$key = $this->key;
		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;	
		}
		return base64_encode($result);
	}
	function decrypt($string) {
		$result = '';
		$key = $this->key;
		$string = base64_decode($string);
		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}
		return $result;
	}
}

?>
