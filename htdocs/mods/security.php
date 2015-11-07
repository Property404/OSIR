<?php
	/*
		This is a static class containing
		functions related to cryptographic
		security, e.g. password hashing.
	*/
	
	class Security{
		/*
			Constants:
			Feel free to adjust these as needed
			before you use OCP for the first
			time, IF you know what you're doing.
			
			*_HASH_ROUNDS should be set as high
			as tolerable.
			
			*_SALT_LENGTH should be >=64 bits
			but less than _HASH_LENGTH. A 128
			bit salt is more than sufficient.
			
			*MIN_PASSWORD_LENGTH should be 
			adjusted to >=8 characters in order
			to be actually effective.
		*/
		
		//Public Constants
		const DEFAULT_PASSWORD="hunter2";
		const MIN_PASSWORD_LENGTH=4;//characters
		
		//"Private" Constants
		const _SALT_LENGTH=128;//bits
		const _HASH_LENGTH=256;//bits
		const _HASH_ALGO="sha256";
		const _HASH_ROUNDS=165432;/*Hash rounds are delicious.*/

		//Generate psuedorandom salt
		private static function genSalt(){
			return bin2hex(
				openssl_random_pseudo_bytes(Security::_SALT_LENGTH/8)//convert bits to bytes (divide by 8)
			);
		}

		//Dealing with hashes
		public static function makeSaltedHash($password, $salt = null){
			//Generate salt
			if ($salt == null){
				$salt = self::genSalt();
			}else
				
			//Trim if "$salt" is actually another hash
			if (strpos($salt,"$")){
				$salt = substr($salt,0,self::_SALT_LENGTH/4);
			}
			
			//Return hash prepended with salt
			return $salt . "$" . hash_pbkdf2(self::_HASH_ALGO,$password,$salt,self::_HASH_ROUNDS,self::_HASH_LENGTH/4);
		}
		
	}

?>
