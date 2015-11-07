/*
	OSIR's semi-generic encryption
	interface
*/
#ifndef HEADER_CRYPT
	#define HEADER_CRYPT
	#include <stdbool.h>

	//Symmetric encryption and decryption
	unsigned int symEncrypt(char** ciphertext,const char* key,const char* plaintext, const unsigned int ptsize);
	unsigned int symDecrypt(char** plaintext, const char* key, const char* ciphertext, const unsigned int ctsize);

	//Secure random number generation
	bool secureRand(char** output,int size);
	
	//Generate random key (secureRand with size SYMMETRIC_KEY_SIZE)
	bool genKey(char** output);
#endif
