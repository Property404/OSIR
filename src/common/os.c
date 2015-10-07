#include "common.h"
#include "os.h"
#include "thirdparty/dirent.h"
#include <time.h>
#include <sys/utime.h>
#define MAX_FILE_NAME_LENGTH 255
#define HOUR 3600
#define MARCH 2
#define NOVEMBER 10


char** walkDir(const char* top){
	//Define directory and stat structs
	DIR *dir;
	struct dirent *ent;
	struct stat statbuf;
	
	//Other variables
	char* entpath;
	char** ls=(char**)malloc(1*sizeof(char*));
	ls[0]=(char*)malloc(1*sizeof(char));
	strcpy(ls[0],"\0");
	unsigned int lscount=0;
	
	//Recursively cycle through directories
	if ((dir = opendir (top)) != NULL) {
		while ((ent = readdir (dir)) != NULL) {
			
				//Get full path of entity;
				entpath=(char*) malloc(strlen(ent->d_name)+strlen(top)+2);
				strcpy(entpath,top);
				strcat(entpath,"/");
				strcat(entpath,ent->d_name);
				
				//Get path attributes
				stat(entpath, &statbuf);
				
				//Check if file or directory
				if(S_ISDIR(statbuf.st_mode)){
					
					//Do not include the "." and ".." directories
					if(strcmp(ent->d_name,".\0") && strcmp(ent->d_name,"..\0")){
						
						//Add files from subdirectory
						char** newls=(char**)malloc(1);
						newls=walkDir(entpath);
						for(unsigned int i=0;strcmp(newls[i],"\0");i++){
							
							//Reallocate ls
							ls=(char**)realloc(ls,(lscount+2)*sizeof(char*));
							if(ls==NULL){fprintf(stderr,"Error: walkDir: Allocation Error\n");break;}
							
							//Add new file to ls
							ls[lscount]=(char*)malloc(strlen(newls[i])+1);
							strcpy(ls[lscount],newls[i]);
							
							//Append null character
							ls[++lscount]=(char*)malloc(1);
							strcpy(ls[lscount],"\0");
							
							//Free memory
							free(newls[i]);							
						}
						free(newls);
					}
				}else{
					//Reallocate ls
					ls=(char**)realloc(ls,(lscount+2)*sizeof(char*));
					if(ls==NULL){fprintf(stderr,"Error: walkDir: Allocation Error\n");break;}
					
					//Add new file to ls
					ls[lscount]=(char*)malloc(strlen(entpath)+1);
					strcpy(ls[lscount],entpath);
					
					//Append null character
					ls[++lscount]=(char*)malloc(1);
					strcpy(ls[lscount],"\0");
				}
		}
		closedir(dir);
	}
	return ls;
}

bool setFileModifiedDate(const char* filename,time_t time){
	//initialize ut struct
	struct _utimbuf ut;
	
	//Modify  times in struct
	time-=HOUR;
	ut.actime=ut.modtime=mktime(gmtime(&time));
	
	//Change file's date modified(1 if successful)
	return (_utime(filename,&ut)!=-1);
}

time_t getFileModifiedDate(const char* filename){
	struct stat attrib;
	if(stat(filename, &attrib)!=0)return -1;
	
	//Adjust for timezones
	struct tm gm_tm=*gmtime(&(attrib.st_mtime));
	struct tm lcl_tm=*localtime(&(attrib.st_mtime));
	time_t dif=mktime(&lcl_tm)-mktime(&gm_tm);
	
	//Adjust for DST
	if((gm_tm).tm_mon>=MARCH && (gm_tm).tm_mon<NOVEMBER)dif+=HOUR;
	
	//Return time as time_t int
	return attrib.st_mtime+dif;
}

char* getFileExtension(const char* filename){
	//Use volatile to keep optimized code from breaking
	volatile char extension[MAX_FILE_NAME_LENGTH]="\0"; 
	
	//Find extension
	for(int i=0,j=-1;filename[i]!='\0';i++){
		
		//If dot is found, start recording extension
		if(j>=0){
			extension[j]=tolower(filename[i]);
			extension[++j]='\0';
		}
		if(filename[i]=='.'){
			j=0;
			extension[0]='\0';
		}
		//Cancel recording if slash found
		if(filename[i]=='/' || filename[i]=='\\'){
			j=-1;
			extension[0]='\0';
		}
	}                  
              
	char* ret_extension=(char*)extension;
	return ret_extension;
}

struct Executable* getExecType(const char* filename){
	//Initialize exectype
	Executable* exectype=(Executable*)malloc(sizeof(*exectype));
	exectype->is_win=0;
	exectype->is_elf=0;
	exectype->is_native=0;
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
	char* binary=(char*)malloc(sizeof(char)*bf_size);
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
	else if(binary[0]=='\x7f' && binary[1]=='E' && binary[2]=='L' && binary[3]=='F'){
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
	struct stat64 st;
	stat64(filename, &st);
	return st.st_size;
}
