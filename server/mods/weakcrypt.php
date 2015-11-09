<?php
	/*
		This is a static class dealing with
		the weak 'encryption' between the
		OSIR server and client.
		
		Note this 'encryption' is absolutely
		non-protective against all human
		attackers, because it contains the
		symmetric key, prepended to the
		encrypted message.
		
		It is utilized to prevent antivirus
		from detecting malicious binaries
		over a non-encrypted connection.
	*/
	
	class Weakcrypt{
		//64-bit keysize(8*8 bits)
		const _XOR_KEY_SIZE=8;
		
		//Simple-XOR encryption
		static function xorString($key,$msg){
			//Initialize ciphertext
			$ciphertext="";
			
			//Encrypt $message
			for($i=0;$i<strlen($msg);$i++)
				$ciphertext.=($msg[$i] ^ $key[$i%self::_XOR_KEY_SIZE]);
			return $ciphertext;
		}

		//Encrypt Message
		static function encrypt($msg){
			//Securely generate key
			$key=openssl_random_pseudo_bytes(self::_XOR_KEY_SIZE);
			
			//Add "integrity" byte (minimal error checking)
			//XOR first and last byte
			$msg.=$msg[0]^$msg[strlen($msg)-1];
			
			//Return simple XOR encryption with generated key
			return base64_encode($key.self::xorString($key,$msg));
		}
		
		//Decrypt Message
		static function decrypt($msg){
			//Decode message
			$msg=base64_decode($msg);
			
			//Extract key
			$key=substr($msg,0,self::_XOR_KEY_SIZE);
			$msg=substr($msg,self::_XOR_KEY_SIZE);
			
			//Decrypt message;
			$cleartext=self::xorString($key,$msg);
			
			//Check integrity
			if(($cleartext[0] ^ $cleartext[strlen($cleartext)-2])
							!=$cleartext[strlen($cleartext)-1])
			echo("<div class='alert'>Integrity Check Failed</div>\n");
			
			//Return cleartext minus integrity byte
			return substr($cleartext,0,strlen($cleartext)-1);
		}
		
	};

?>