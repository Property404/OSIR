/*
  Operating system related structs and functions
*/
#ifndef HEADER_OS
	#define HEADER_OS 1
	#include <stdbool.h>
	#include <stdint.h>

	//Structure of executable program
	typedef struct Executable {
		//overall;
		bool is_exec;
		
		//System
		bool is_win;//Windows
		bool is_elf;//Linux
		bool is_native;
		
		
	} Executable;

	//Walk through directory
	char** walkDir(const char*);
	
	//Set/get File Time
	bool setFileModifiedDate(const char*, time_t);
	time_t getFileModifiedDate(const char* filename);
	
	//Check file extension
	char* getFileExtension(const char* filename);
	
	//Get executable type
	struct Executable* getExecType(const char* filename);

	//Get file size in bytes (-1 means error)
	int64_t getFileSize(const char* filename);

#endif
