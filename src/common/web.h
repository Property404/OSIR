/* 
	This module provides an
	interface for HTTP w/ a server
*/

//Send HTTP request to server
int sendHTTPRequest(char **reply,const char* hostname, const char* request);

//Send GET request and receive body of response
char* getHTML(const char* url);
