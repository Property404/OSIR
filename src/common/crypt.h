/*
	OSIR's encryption interface
*/
const int RSA_KEY_SIZE=1024;//bits
const int AES_KEY_SIZE=128;//bits
const int AES_IV_SIZE=64;//bits

//Symmetric encryption;
int aesEncrypt(char** ciphertext,char* key,char* plaintext);
int aesDecrypt(char** plaintext,char* key,char* ciphertext);
char** generateKey(int size);

//Asymmetric encryption
int rsaEncrypt(char** ciphertext,char* pubkey,char* plaintext);
