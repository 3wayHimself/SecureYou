<?php
/**
 *  SecureYou 1.0 - PHP Library For Login/Register
 *
 *  @author Alemalakra
 *  @version 1.0
 */

class SecureYou {
	function __construct($PDO) {
		if(!($PDO instanceof PDO)) {
			die('Inavlid PDO Connection!');
		}
		$this->db = $PDO;
	}
	function getError() {
		if (isset($this->Error)) {
			return $this->Error;
		} else {
			return 'No Error To Display';
		}
	}
	function isLogged() {
		@session_start();
		if (isset($_SESSION['session'])) {
		    $stmt = $this->db->prepare("SELECT * FROM sessions WHERE session = :session");
		    $stmt->bindParam(':session', $_SESSION['session']);
		    $stmt->execute();

		    if($stmt->rowCount() > 0){
		    	$result = $stmt->fetchAll();
		    	if ($_SESSION['userid'] == $result[0]['uid']) {
		    		return true;
		    	} else {
		    		return false;
		    	}
		    	return true;
		    } else {
		    	return false;
		    }
		} else {
			return false;
		}
	}
	function isAlphaNumeric($string) {
		return preg_match("/^[a-zA-Z0-9\s@.]*$/", $string);
	}
	function isValidEmail($string) {
		if(filter_var($string, FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {
			return false;
		}
	}
	function register($user, $email, $password) {
		if (!($this->isValidEmail($email))) {
			$this->Error = 'Invalid Email';
			return false;
		}
		if (!($this->isAlphaNumeric($user))) {
			$this->Error = 'Username Can Contains Only AlphaNumeric Letters';
			return false;
		}
		if (strlen($user) > 16) {
			$this->Error = 'Username Can Max 16 Caracters Length';
			return false;
		}
		if (strlen($password) > 32) {
			$this->Error = 'Password Can Max 32 Caracters Length';
			return false;
		}
		if (strlen($email) > 32) {
			$this->Error = 'Email Can Max 32 Caracters Length';
			return false;
		}
		$stmt = $this->db->prepare("INSERT INTO users (email, username, password)VALUES (:email, :username, :password)");
		$stmt -> execute(array(':email' => $email, ':username' => $user, ':password' => password_hash($password, PASSWORD_DEFAULT)));
		$this->login($user, $password);
		return true;
	}
	function createSession($userid) {
		$session = substr(md5(rand()), 0, 35);
		$stmt = $this->db->prepare("INSERT INTO sessions (uid, session)VALUES (:uid, :session)");
		$stmt -> execute(array(':uid' => $userid, ':session' => $session));
		$_SESSION['session'] = $session;
	}
	function login($user, $password) {
		if (strlen($user) > 16) {
			$this->Error = 'Username Can Max 16 Caracters Length';
			return false;
		}
		if (strlen($password) > 32) {
			$this->Error = 'Password Can Max 32 Caracters Length';
			return false;
		}
		if (!($this->isAlphaNumeric($user))) {
			$this->Error = 'Username Can Contains Only AlphaNumeric Letters';
			return false;
		}
	    $stmt = $this->db->prepare("SELECT username FROM users WHERE username = :name");
	    $stmt->bindParam(':name', $user);
	    $stmt->execute();

	    if($stmt->rowCount() > 0){
	        
		    $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :name");
		    $stmt->bindParam(':name', $user);
		    $stmt->execute();
		    $result = $stmt->fetchAll();
		    if (password_verify($password, $result[0]['password'])) {
		    	$_SESSION['username'] = $user;
		    	$_SESSION['userid'] = $result[0]['id'];
		    	$_SESSION['useremail'] = $result[0]['email'];
		    	$this->createSession($_SESSION['userid']);
		    	$this->Username = $user;
		    	return true;
		    } else {
		    	$this->Error = 'Invalid Password';
		    	return false;
		    }


	    } else {
	    	$this->Error = 'Username No Exists';
	        return false;
	    }

	}
	function userexist($user, $email) {
	    
	    $stmt = $this->db->prepare("SELECT username FROM users WHERE username = :name");
	    $stmt->bindParam(':name', $user);
	    $stmt->execute();

	    if($stmt->rowCount() > 0){
	        return true;
	    }
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

?>
