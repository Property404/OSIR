#include "crypt.h"
#include "common.h"
#include <stdio.h>
#ifdef _WIN32
	#include "windows.h"
	#include "wincrypt.h"
#endif

//Return random bytes
bool secureRand(char** output,const int size){
	//Allocate bytes
	*output=(char*)malloc(sizeof(char)*size);
	#ifdef _WIN32
		HCRYPTPROV hCryptProv=0;
		
		//Prepare CSPRNG
		if (!CryptAcquireContextW(&hCryptProv, 0, 0, PROV_RSA_FULL, CRYPT_VERIFYCONTEXT | CRYPT_SILENT)){
			fprintf(stderr,"Error: secureRand: CryptAcquireContextW failed");
			return 0;
		}
		
		//Generate bytes
		if(!CryptGenRandom(hCryptProv,size,(unsigned char*)*output)){
			fprintf(stderr,"Error: secureRand: CryptGenRandom failed");
			return 0;
		}
	#else
		//FIXME: read from /dev/urandom
		for(int i=0;i<size;i++){
			*output[i]=rand()%256-128;
		}
	#endif
	return 1;
}

//Generate key according to const key size
bool genKey(char** output){
	return secureRand(output,SYMMETRIC_KEY_SIZE);
}

/*
Current Prototype's encryption scheme: XOR
This is a placeholder for real encryption
The next prototype should implement TwoFish,
Blowfish, Serpent, or else (ideally) AES
PTO means "ProtoType Only"
*/

//Symmetric encryption
unsigned int symEncrypt(char** ciphertext,const char* key,const char* plaintext, const unsigned int ptsize){
	//Allocate memory for ciphertext
	*ciphertext=(char*)malloc(sizeof(char)*(ptsize+SYMMETRIC_IV_SIZE));
	
	//Generate IV(initialization vector)
	char* iv;
	secureRand(&iv,SYMMETRIC_IV_SIZE);
	
	//Prepend ciphertext with IV
	for(unsigned int i=0;i<SYMMETRIC_IV_SIZE;i++)
		(*ciphertext)[i]=iv[i];

	
	//PTO: encrypt with XOR
	for(unsigned int i=0;i<ptsize;i++)
		(*ciphertext)[SYMMETRIC_IV_SIZE+i]=key[i%SYMMETRIC_KEY_SIZE]^plaintext[i]^iv[i%SYMMETRIC_IV_SIZE];
	
	//Deallocate allocated memory
	free(iv);
	
	//Return size;
	return SYMMETRIC_IV_SIZE+ptsize;
}


//Symmetric Decryption
unsigned int symDecrypt(char** plaintext, const char* key, const char* ciphertext, const unsigned int ctsize){
	//Allocate plaintext memory
	*plaintext=(char*)malloc(sizeof(char)*(ctsize-SYMMETRIC_IV_SIZE));
	
	//Extract IV
	char* iv=(char*)malloc(sizeof(char)*SYMMETRIC_IV_SIZE);
	for(int i=0;i<SYMMETRIC_IV_SIZE;i++)
		iv[i]=ciphertext[i];
	
	//PTO: Decrypt message
	for(unsigned int i=0;i<ctsize-SYMMETRIC_IV_SIZE;i++)
		(*plaintext)[i]=ciphertext[i+SYMMETRIC_IV_SIZE]^key[i%SYMMETRIC_KEY_SIZE]^iv[i%SYMMETRIC_IV_SIZE];
	
	return ctsize-SYMMETRIC_IV_SIZE;
	
}