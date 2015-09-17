#include "os.h"
#include <stdio.h>
#include <stdlib.h>

Executable getExeType(char* filename){
	//Define exectype
	Executable exectype;
	exectype.is_win=0;
	exectype.is_elf=0;
	exectype.is_msi=0;
	exectype.is_dll=0;
	exectype.is_exec=0;
	
	//Open binary file
	FILE* bf=fopen(filename,"rb");
	if(bf==NULL){
		printf("Warning - os.c - problem opening file\n");
		return exectype;
	}
	
	//check size
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
		exectype.is_win=1;
		exectype.is_exec=1;
	}
	
	//Linux
	if(binary[0]=='\x7f' && binary[1]=='E' && binary[2]=='L' && binary[3]=='F'){
		exectype.is_elf=1;
		exectype.is_exec=1;
	}
	
	//Free memory
	free(binary);
	
	//Return executable type
	return exectype;
}


int64_t getFileSize(char* filename){
	//Open file
	FILE* fp=fopen(filename,"rb");
	if(fp==NULL)return -1;

	//check size
	fseek(fp,0L,SEEK_END);
	return ftell(fp);
	
}
