/*
  Header for the third party source file b64.c
  Modified for OSIR by Dagan Martinez
*/
#ifndef HEADER_B64
#define HEADER_B64
	//base64 decoding - returns size of output
unsigned int b64decode(char **output, const char *intext);

	//base16(hexadecimal) encoding/decoding
char *b16encode(const char *intext, unsigned int length);
char *b16decode(const char *intext);
#endif
