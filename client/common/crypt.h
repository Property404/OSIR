/*
	OSIR's semi-generic encryption
	interface
*/
#ifndef HEADER_CRYPT
	#define HEADER_CRYPT
	#include <stdbool.h>
	#include <stdint.h>
	
	//Symmetric encryption and decryption
	bool symEncrypt(char** ciphertext,const char* keyiv,const char* plaintext, const int64_t);
	bool symDecrypt(char** plaintext, const char* keyiv, const char* ciphertext, const int64_t);

	//Secure random number generation
	bool secureRand(char** output,int size);
#endif
