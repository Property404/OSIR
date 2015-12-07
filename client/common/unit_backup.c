#include "thirdparty/aes.h"
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>

#define failure(x) fprintf(stderr,"Failure: %s\n", x);exit(1)
#define error(x) fprintf(stderr, "%Error: %s\n", x)

static void phex(uint8_t* str)
{
    unsigned char i;
    for(i = 0; i < 16; ++i)
        printf("%c", (char)str[i]);
    printf("\n");
}

void encrypt(char* ciphertext, char* plaintext, uint32_t length, const char* key, const char* iv){
	uint8_t *iciph=malloc(length);
	AES128_CBC_encrypt_buffer(iciph, (uint8_t *)plaintext, length, (uint8_t *)key, (uint8_t *)iv);
}

int main()
{
		uint8_t key[16] = { (uint8_t) 0x2b, (uint8_t) 0x7e, (uint8_t) 0x15, (uint8_t) 0x16, (uint8_t) 0x28, (uint8_t) 0xae, (uint8_t) 0xd2, (uint8_t) 0xa6, (uint8_t) 0xab, (uint8_t) 0xf7, (uint8_t) 0x15, (uint8_t) 0x88, (uint8_t) 0x09, (uint8_t) 0xcf, (uint8_t) 0x4f, (uint8_t) 0x3c };
		
		 uint8_t plain_text[64] = { (uint8_t) 'h', (uint8_t) 'i', (uint8_t) ' ', (uint8_t) 'b', (uint8_t) 'u', (uint8_t) 'd', (uint8_t) 'd', (uint8_t) 'y', (uint8_t) '!', (uint8_t) ' ', (uint8_t) 'H', (uint8_t) 'o', (uint8_t) 'w', (uint8_t) ' ', (uint8_t) 'a', (uint8_t) 'r',(uint8_t) 'e', (uint8_t) ' ', (uint8_t) 'y', (uint8_t) 'o', (uint8_t) 'u', (uint8_t) 'r', (uint8_t) ' ', (uint8_t) 'p', (uint8_t) 'a', (uint8_t) 'r', (uint8_t) 'e', (uint8_t) 'n', (uint8_t) 't', (uint8_t) 's', (uint8_t) ' ', (uint8_t) 'd',(uint8_t) 'o', (uint8_t) 'i', (uint8_t) 'n', (uint8_t) 'g', (uint8_t) '?', (uint8_t) ' ', (uint8_t) 'W', (uint8_t) 'e', (uint8_t) 'l', (uint8_t) 'l', (uint8_t) '?', (uint8_t) ' ', (uint8_t) 'T', (uint8_t) 'h', (uint8_t) 'a', (uint8_t) 't',(uint8_t) '\'', (uint8_t) 's', (uint8_t) ' ', (uint8_t) 'g', (uint8_t) 'o', (uint8_t) 'o', (uint8_t) 'd', (uint8_t) ' ', (uint8_t) 't', (uint8_t) 'o', (uint8_t) ' ', (uint8_t) 'h', (uint8_t) 'e', (uint8_t) 'a', (uint8_t) 'r', (uint8_t) '.' };

		printf("plain text:\n");
		for(uint8_t i = (uint8_t) 0; i < (uint8_t) 4; ++i)
		{
			phex(plain_text + i * (uint8_t) 16);
		}
		
		uint8_t cipher_text[64];
		uint8_t decrypted_text[64];
		memset(cipher_text,'?',64);
		memset(decrypted_text,'?',64);
		printf("Encrypting\n");
		encrypt(cipher_text,plain_text,64,key,key);
		//AES128_CBC_encrypt_buffer(cipher_text, plain_text, 64, key, key);
		printf("Decrypting\n");
		AES128_CBC_decrypt_buffer(decrypted_text, cipher_text, 64, key, key);
		printf("plain Text:%s\n",(char *) plain_text);
		
		
}
