/*
  File meant to allow compatibility between
  GCC/TCC, C/C++, and Windows/Linux
*/

#ifndef HEADER_COMPATIBILITY
	#define HEADER_COMPATIBILITY 1

	//TinyCC does not define _WIN32
	#ifdef __tinycc__
		#ifndef __linux__
			#define _WIN32
		#endif
	#endif

#endif