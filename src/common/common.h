#ifndef HEADER_COMMON
	#define HEADER_COMMON
	#include "settings.h"
	#ifdef _WIN32
		#define stat64 _stat64
		#define popen _popen
		#define popen _popen
		#define pclose _pclose
	#endif
	#ifndef INT32_MAX
		#define INT32_MAX 2147483647
	#endif
#endif