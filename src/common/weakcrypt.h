/*
  Weak encryption meant to hide
  binaries from other programs
  (not humans)
*/

#ifndef HEADER_WEAKCRYPT
	#define HEADER_WEAKCRYPT 1
	
	//Encrypt msg by xor-ing msg with key
	char* xorCrypt(const char*, unsigned int, const char*, unsigned int);
#endif
