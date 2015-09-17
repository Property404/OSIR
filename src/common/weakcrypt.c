#include "weakcrypt.h"
#include <stdlib.h>

char* xorCrypt(char* key, unsigned int keylength, char* msg, unsigned int msglength){
	//allocate memory for ciphertext output
	char* ciphertext=(char*)malloc(msglength);
	
	//xor msg with key
	for(int i=0;i<msglength;i++){
		ciphertext[i]=key[i%keylength] ^ msg[i];
		ciphertext[i+1]='\0';
	}
	
	return ciphertext;
}