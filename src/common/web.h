/* 
	This module provides OSIR
	an interface with the server
*/
#ifndef HEADER_WEB
	#define HEADER_WEB
	//Send HTTP request to server
	int sendHTTPRequest(char **reply,const char* hostname, const char* request);

	//Send GET request and receive body of response(text only)
	char* getHypertext(const char* url);
#endif
