<?php
/**
 *  SecureYou 1.0 - PHP Library For Login/Register
 *
 *  @author Alemalakra
 *  @version 1.0
 */

class SecureYou {
	function __construct($conn) {
		$this->db = $conn;
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
	function register($user, $email, $password) {
		$stmt = $this->db->prepare("INSERT INTO users (email, username, password)VALUES (:email, :username, :password)");
		$stmt -> execute(array(':email' => $email, ':username' => $user, ':password' => $this->encrypt($password)));
		$this->login($user, $password);
	}
	function createSession($userid) {
		$session = substr(md5(rand()), 0, 35);
		$stmt = $this->db->prepare("INSERT INTO sessions (uid, session)VALUES (:uid, :session)");
		$stmt -> execute(array(':uid' => $userid, ':session' => $session));
		$_SESSION['session'] = $session;
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
		    if (password_verify($password, $result[0]['password'])) {
		    	$_SESSION['username'] = $user;
		    	$_SESSION['userid'] = $result[0]['id'];
		    	$_SESSION['useremail'] = $result[0]['email'];
		    	$this->createSession($_SESSION['userid']);
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
		return password_hash($string, PASSWORD_DEFAULT);
	}
}

?>
