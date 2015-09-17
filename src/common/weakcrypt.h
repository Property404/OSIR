/*
  Weak encryption meant to hide
  binaries from other programs
  (not humans)
*/

#ifndef HEADER_WEAKCRYPT
	#define HEADER_WEAKCRYPT 1
	
	//Encrypt with key by XOR-ing
	char* xorCrypt(char* key, unsigned int keylength, char* msg, unsigned int msglength);
#endif