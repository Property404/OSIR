#include "paths.h"
#include "common.h"
#include <string.h>
#include <stdlib.h>
#include <stdio.h>

bool getHomePath(char* path)
{
	/*Get home directory from OS_specific
	environmental variables*/
	const char *home_dir = getenv(
#ifdef _WIN32
	"USERPROFILE"
#else
	"HOME"
#endif
	);
	
	//Check for errors. If none, copy string
	if(home_dir == NULL)
		return false;
	else
		strcpy(path, home_dir);
	return true;

}

bool getExternalPaths(char** paths){
	FILE* fp;
	const int line_size = 256;
	const char* home_dir = getenv("USERPROFILE");
	char current_drive[line_size];
	//Error check
	if(home_dir==NULL){
		fprintf(stderr, "Error: getExternalPaths: getenv failed\n");
		return 0;
	}
	
	//Get current drive
	memset(current_drive, '\0', line_size);
	for(int i=0;i<line_size;i++)
		if(home_dir[i]==':'){
			memcpy(current_drive, &home_dir[0],i+1);
			printf("!\n");
			break;
		}
	printf("CD: %s\n", current_drive);
	
	//Open command as file
	fp = popen("wmic logicaldisk get caption", "r");
	
	//Error check
	if(fp==NULL){
		fprintf(stderr, "Error: getExternalPaths: unable to popen command\n");
		return 0;
	}

	//Allocate memory for path list
	paths=(char **)malloc(1);
	paths[0]=(char *)malloc(line_size);
	
	//Read lines from pfile
	int i=0;
	fgets(paths[0], line_size, fp);
	for(i=0; fgets(paths[i], line_size, fp) != NULL; i++){
		for(int j=0; j<line_size ;j++){
			if(paths[i][j]<=' '){
				paths[i][j]='\0';
				break;
			}
		}
		if(!strcmp(paths[i], current_drive))i--;
		else{
			printf(":%s/",paths[i]);
			paths = (char **)realloc(paths, i+1);
			paths[i+1] = (char*)malloc(line_size);
		}
	}
	//Add ending lines
	paths[i][0]='\0';
	
	//Return successfully
	return 1;
	
}