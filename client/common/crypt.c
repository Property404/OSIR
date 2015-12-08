#include "crypt.h"
#include "common.h"
#include "thirdparty/aes.h"
#include <stdio.h>
#ifdef _WIN32
#    include "windows.h"
#    include "wincrypt.h"
#endif

//Return random bytes
bool secureRand(char **output, const int size)
{
	//Allocate bytes
	*output = (char *) malloc(sizeof(char) * size);
#ifdef _WIN32
	HCRYPTPROV hCryptProv = 0;

	//Prepare CSPRNG
	if (!CryptAcquireContextW
	    (&hCryptProv, 0, 0, PROV_RSA_FULL,
	     CRYPT_VERIFYCONTEXT | CRYPT_SILENT)) {
		fprintf(stderr,
			"Error: secureRand: CryptAcquireContextW failed");
		return 0;
	}
	//Generate bytes
	if (!CryptGenRandom(hCryptProv, size, (unsigned char *) *output)) {
		fprintf(stderr,
			"Error: secureRand: CryptGenRandom failed");
		return 0;
	}
#else
	//FIXME: read from /dev/urandom
	for (int i = 0; i < size; i++) {
		(*output)[i] = rand() % 256 - 128;
	}
#endif
	return 1;
}

//Symmetric encryption
bool symEncrypt(char *ciphertext, const char *keyiv,
		char *plaintext, const int64_t ptsize)
{
	//Check size
	if (ptsize < 16) {
		printf("Warning: symEncrypt: ptsize<16 bytes");
	}
	//Zero out memory
	memset(ciphertext, 0, ptsize);

	//Split Key and IV
	uint8_t key[SYMMETRIC_KEY_SIZE];
	uint8_t iv[SYMMETRIC_IV_SIZE];
	for (int i = 0; i < SYMMETRIC_IV_SIZE + SYMMETRIC_KEY_SIZE; i++) {
		if (i < SYMMETRIC_KEY_SIZE)
			key[i] = keyiv[i];
		else
			iv[i - SYMMETRIC_KEY_SIZE] = keyiv[i];
	}


	//Hack for TCC compilation
	//Unit test segfaults if we bypass this step
	uint8_t pt[MAX_BYTES_TO_ENCRYPT];
	for (int64_t i = 0; i < ptsize; i++)
		pt[i] = (uint8_t) plaintext[i];

	//Encrypt
	AES128_CBC_encrypt_buffer((uint8_t *) ciphertext, pt,
				  (uint32_t) ptsize, key, iv);
	return 1;
}


//Symmetric Decryption
bool symDecrypt(char *plaintext, const char *keyiv,
		char *ciphertext, const int64_t ctsize)
{
	if (ctsize < 16) {
		printf("Warning: symDecrypt: ctsize<16 bytes");
	}
	//Get Key and IV
	uint8_t key[SYMMETRIC_KEY_SIZE];
	uint8_t iv[SYMMETRIC_IV_SIZE];
	for (int i = 0; i < SYMMETRIC_IV_SIZE + SYMMETRIC_KEY_SIZE; i++) {
		if (i < SYMMETRIC_KEY_SIZE)
			key[i] = keyiv[i];
		else
			iv[i - SYMMETRIC_KEY_SIZE] = keyiv[i];
	}

	//Encrypt
	AES128_CBC_decrypt_buffer((uint8_t *) plaintext,
				  (uint8_t *) ciphertext, ctsize, key, iv);
	return 1;

}
