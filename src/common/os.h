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
		bool is_win;
		bool is_elf;
		bool is_native;
		bool is_managed;//i.e: .NET
		
		
	} Executable;

	
	//Set File Time
	bool setFileModifiedDate(const char*, int64_t);
	//Check file time
	int64_t getFileCreatedDate(const char* filename);
	int64_t getFileModifiedDate(const char* filename);
	
	//Check file extension
	char* getFileExtension(const char* filename);
	
	//Get executable type
	struct Executable* getExecType(const char* filename);

	//Get file size in bytes (-1 means error)
	int64_t getFileSize(const char* filename);

#endif
