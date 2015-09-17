/*
  Operating system related structs and functions
*/
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
	
} Executable;


//Get executable type
Executable getExeType(char* filename);

//Get file size in bytes (-1 means error)
int64_t getFileSize(char* filename);
