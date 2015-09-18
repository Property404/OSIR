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
		
		//Windows
		bool is_msi;
		bool is_dll;
		bool is_managed;//.NET
		
		
	} Executable;


	//Get executable type
	struct Executable* getExecType(const char* filename);

	//Get file size in bytes (-1 means error)
	int64_t getFileSize(const char* filename);

#endif
