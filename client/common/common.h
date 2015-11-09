/*
	This contains cross-file definitions
*/
#ifndef HEADER_COMMON
	#define HEADER_COMMON
	#include "settings.h"
	
	//Ease cross-platform writing
	#ifdef _WIN32
		#define stat64 _stat64
		#define popen _popen
		#define popen _popen
		#define pclose _pclose
	#endif
	
	//Max size of 32-bit integer
	#ifndef INT32_MAX
		#define INT32_MAX 2147483647
	#endif
	
	//Cryptographical macros
	#define SYMMETRIC_KEY_SIZE 128/8	//bytes
	#define SYMMETRIC_IV_SIZE 64/8		//bytes

	//Replication macros
	#define XOR_KEY_SIZE 8 // Do not change w/o changing weakcrypt.php
	#define END_MARKER "xj9"
	#define END_MARKER_SIZE 3
#endif
