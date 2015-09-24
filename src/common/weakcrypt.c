#include "weakcrypt.h"
#include <stdlib.h>

char* xorCrypt(const char* key, unsigned int keylength, const char* msg, unsigned int msglength){
	//allocate memory for ciphertext output
	char* ciphertext=(char*)malloc(sizeof(char)*msglength);
	
	//xor msg with key
	unsigned int i;
	for(i=0;i<msglength;i++){
		ciphertext[i]=key[i%keylength] ^ msg[i];
	}
	ciphertext[i+1]='\0';//Make sure string ends with null char

	
	return ciphertext;
}
