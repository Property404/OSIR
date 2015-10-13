<?php

class Security{
	//Hashing
	const DEFAULT_PASSWORD="hunter2";
	const _SALT_LENGTH=128;
	const _HASH_ALGO="sha256";
	const _HASH_ROUNDS=123456;/*Hash rounds are delicious.*/

	//Generating salt
	static function genSalt(){
		$raw_salt = openssl_random_pseudo_bytes(Security::_SALT_LENGTH/8);
		return bin2hex($raw_salt);
	}

	//Dealing with hashes
	static function makeSaltedHash($password, $salt = null){
		//Generate salt
		if ($salt == null){
			$salt = Security::genSalt();
		}else
		//Trim if "$salt" is another hash
		if (strpos($salt,"$") !== false){
			$salt = substr($salt,0,Security::_SALT_LENGTH/4);
		}
		return $salt . "$" . hash_pbkdf2(Security::_HASH_ALGO,$password,$salt,Security::_HASH_ROUNDS,64);
	}
	
}
 

?>
