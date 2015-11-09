<?php
	/*
		The KeyPair class provides an OOP
		approach to handling RSA keys.
	*/
	
	class KeyPair{
		//Do not set this less than 1024
		//Anything over 2048 is overkill
		const _DEFAULT_KEY_SIZE=1024;//bits
		

		//Private field variables
		private $private_key;
		private $public_key;
		private $size;
		private $can_decrypt;
		private $id;
		
		//Static methods
		public static function getHighestID($link){
			return mysqli_fetch_row(mysqli_query($link,"SELECT max(ID) from RSAKEYS"))[0];
			
		}
		
		//Public instance methods
		function __construct($can_decrypt=false){
		}
		
		//Generate keys
		public function generate($keysize=self::_DEFAULT_KEY_SIZE){
			//Configure key creation
			$config = array(
				"digest_alg" => "sha256",
				"private_key_bits" => $keysize,
				"private_key_type" => OPENSSL_KEYTYPE_RSA,
			);
			
			//Generate key pair
			$keypair = openssl_pkey_new($config);
			
			//Extract private key
			if(!openssl_pkey_export($keypair,$privkey,null,$config))die("Couldn't extract private key");
			
			//Extract public key
			$pubkey = openssl_pkey_get_details($keypair)["key"];
				
			//Return pair in array form along with size
			$this->public_key=$pubkey;
			$this->private_key=$privkey;
			$this->size=$keysize;
			return $this;
		}
		
		//Extract key info from database
		public function sqlImport($link,$keyid){
			//Fetch key row at $keyid
			$res=mysqli_fetch_row(mysqli_query($link,"SELECT * FROM RSAKEYS WHERE ID=$keyid"));
			
			//Transfer to field
			$this->id=$keyid;
			$this->public_key=$res[1];
			$this->private_key=$res[2];
			$this->size=$res[3];
			
			//Return self
			return $this;
		}
		
		//Check validity of KeyPair object
		public function isValid(){
			return !!($this->public_key && $this->private_key && $this->size);
		}
		
		//Create a new SQL row with key info
		public function sqlExport($link){
			//Find lowest free ID
			$i=1;
			while((new KeyPair())->sqlImport($link,$i)->isValid()){$i++;}
			
			//Insert into database at lowest free ID
			mysqli_query($link,"INSERT INTO RSAKEYS (ID,PUBLIC,PRIVATE,SIZE) VALUES ($i,'".$this->public_key."','".$this->private_key."',".$this->size.")");
		}
		
		//Encrypt  string with RSA
		public function encrypt($msg){
			openssl_public_encrypt($msg,$encrypted_msg,$this->public_key);
			return $encrypted_msg;
		}
		
		//Decrypt string with RSA
		public function decrypt($encrypted_msg){
			if($this->can_decrypt){
				openssl_private_decrypt($encrypted_msg,$msg,$this->private_key);
				return $msg;
			}else
			{
				die("Cannot decrypt. This is illegal");
			}
		}
		
		//Destroy key
		public function destroy($link){
			if($this->id){
				mysqli_query($link,"DELETE FROM RSAKEYS WHERE ID=".$this->id);
			}
		}
		
		//Show key ID and size
		public function toString(){
		return $this->id.': '.$this->size."-bit RSA";
		}
		
		//Getters
		public function getPublic(){return $this->public_key;}
		public function getSize(){return $this->size;}
		public function getID(){return $this->id;}
		
	}
		
?>