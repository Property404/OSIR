<?php

class Security{
	/*
		Constants:
		Feel free to adjust these as needed
		before you use OCP for the first
		time, if you know what you're doing.
		*_HASH_ROUNDS should be set as high
		as tolerable.
	*/
	
	//Public Constants
	const DEFAULT_PASSWORD="hunter2";
	const MIN_PASSWORD_LENGTH=4;//characters
	
	//Private Constants
	const _SALT_LENGTH=128;//bits
	const _HASH_LENGTH=256;//bits
	const _HASH_ALGO="sha256";
	const _HASH_ROUNDS=165432;/*Hash rounds are delicious.*/

	//Generating salt
	private static function genSalt(){
		$raw_salt = openssl_random_pseudo_bytes(Security::_SALT_LENGTH/8);
		return bin2hex($raw_salt);
	}

	//Dealing with hashes
	public static function makeSaltedHash($password, $salt = null){
		//Generate salt
		if ($salt == null){
			$salt = Security::genSalt();
		}else
			
		//Trim if "$salt" is actually another hash
		if (strpos($salt,"$") !== false){
			$salt = substr($salt,0,Security::_SALT_LENGTH/4);
		}
		return $salt . "$" . hash_pbkdf2(Security::_HASH_ALGO,$password,$salt,Security::_HASH_ROUNDS,Security::_HASH_LENGTH/4);
	}
	
}
 

?>
