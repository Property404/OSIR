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

/*
Current Prototype's encryption scheme: XOR
This is a placeholder for real encryption
The next prototype should implement TwoFish,
Blowfish, Serpent, or else (ideally) AES
PTO means "ProtoType Only"
*/

//Symmetric encryption
bool symEncrypt(char** ciphertext,const char* keyiv,const char* plaintext, const int64_t ptsize){
	//PTO: encrypt with XOR
	for(unsigned int i=0;i<ptsize;i++)
		(*ciphertext)[i]=keyiv[i%SYMMETRIC_KEY_SIZE]^plaintext[i]^keyiv[SYMMETRIC_KEY_SIZE+i%SYMMETRIC_IV_SIZE];
	
	
	//Return size;
	return true;
}


//Symmetric Decryption
bool symDecrypt(char** plaintext, const char* keyiv, const char* ciphertext, const int64_t ctsize){
	
	//Extract IV
	char* iv=(char*)malloc(sizeof(char)*SYMMETRIC_IV_SIZE);
	for(int i=0;i<SYMMETRIC_IV_SIZE;i++)
		iv[i]=keyiv[SYMMETRIC_KEY_SIZE+i];
	
	//PTO: Decrypt message
	for(int64_t i=0;i<ctsize;i++)
		(*plaintext)[i]=ciphertext[i+SYMMETRIC_IV_SIZE]^keyiv[i%SYMMETRIC_KEY_SIZE]^iv[i%SYMMETRIC_IV_SIZE];
	
	return true;
	
}