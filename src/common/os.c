#include "os.h"
#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <sys/stat.h>
#include <sys/utime.h>
#define MAX_FILE_NAME_LENGTH 255

bool setFileModifiedDate(const char* filename,int64_t time){
	//initialize struc
	struct tm tma={0,0,0,0,0,0,0,0,0}, tmm={0,0,0,0,0,0,0,0,0};
	struct _utimbuf ut;
	//Convert time
	time_t curtime=time-3600;
	tma=tmm=*gmtime(&curtime);
	ut.actime=ut.modtime=mktime(&tma);
	//Change file's date modified(1 if successful)
	return (_utime(filename,&ut)!=-1);
}

int64_t getFileCreatedDate(const char* filename){
	struct stat st;
	if(stat(filename,&st)!=0)return -1;
	return st.st_ctime;
}

int64_t getFileModifiedDate(const char* filename){
	struct stat attrib;
	if(stat(filename, &attrib)!=0)return -1;
	return mktime(gmtime(&(attrib.st_mtime)))-3600*9;
}

char* getFileExtension(const char* filename){
	char extension[MAX_FILE_NAME_LENGTH]="\0"; 
	//Find extension
	for(int i=0,j=-1;filename[i]!='\0';i++){
		//If dot is found, start recording extension
		if(j>=0){
			extension[j]=filename[i];
			extension[++j]='\0';
		}
		if(filename[i]=='.'){
			j=0;
			extension[0]='\0';
		}
	}                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
	
	//Return extension
	char* ret_extension=extension;
	return ret_extension;
}

struct Executable* getExecType(const char* filename){
	//Initialize exectype
	Executable* exectype=(Executable*)malloc(sizeof(*exectype));
	exectype->is_win=0;
	exectype->is_elf=0;
	exectype->is_native=0;
	exectype->is_managed=0;
	exectype->is_exec=0;

	//Open binary file
	FILE* bf=fopen(filename,"rb");
	if(bf==NULL){
		printf("Warning - os.c - problem opening file\n");
		return exectype;
	}
	
	//check file size
	fseek(bf,0,SEEK_END);
	unsigned int bf_size=ftell(bf);
	fseek(bf,0,SEEK_SET);
	if(bf_size==0){printf("Warning - os.c - bf_size==0\n");return exectype;}

	//Read and close file
	char* binary=(char*)malloc(sizeof(char*)*bf_size);
	fread(binary,1,bf_size,bf);
	fclose(bf);
	
	//Windows
	if(binary[108]=='D' && binary[109]=='O' && binary[110]=='S'){
		exectype->is_win=1;
		exectype->is_exec=1;
		#ifdef _WIN32
			exectype->is_native=1;
		#endif
	}
	
	//Linux
	if(binary[0]=='\x7f' && binary[1]=='E' && binary[2]=='L' && binary[3]=='F'){
		exectype->is_elf=1;
		exectype->is_exec=1;
		#ifdef __linux__
			exectype->is_native=1;
		#endif
	}
	
	//Free memory
	free(binary);
	
	//Return executable type
	return (exectype);
}


int64_t getFileSize(const char* filename){
	//Open file
	FILE* fp=fopen(filename,"rb");
	if(fp==NULL){return -1;}
	
	//check size
	fseek(fp,0,SEEK_END);
	int64_t fp_size=ftell(fp);
	fclose(fp);
	
	return fp_size;
	
}
