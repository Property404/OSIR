<?php
	class Crypt{
		
		//Generate 2 RSA key pairs
		public static function genKeyPair($keysize){
			//Configure key creation
			$config = array(
			"digest_alg" => "sha256",
			"private_key_bits" => $keysize,
			"private_key_type" => OPENSSL_KEYTYPE_RSA,
			);
			
			//Generate key pair
			$keypair = openssl_pkey_new($config);
			
			//Extract private key
			if(!openssl_pkey_export($keypair,$privkey,NULL,$config))die("Couldn't extract private key");
			
			//Extract public key
			$pubkey = openssl_pkey_get_details($keypair)["key"];
			
			//Return pair in array form along with size
			return array(
				"public" => $pubkey,
				"private" => $privkey,
				"size" => $keysize);
		}
	}
	echo(Crypt::genKeyPair(1024)["public"]);
?>