#include "../../client/common/web.h"
#include "../../client/common/common.h"
#include <stdio.h>

int main(int argc, char** argv){argc=argc;
	int status;

	//Check internet connection
	char** reply;
	status=sendHTTPRequest(reply,"208.80.154.224","GET /wiki/Main_Page http/1.1\r\nHost: en.wikipedia.org\r\n\r\n");
	printf("Connection Status(wikipedia):\t%s\n",status?"OK":"Failed");
	if(!status)exit(0);
	
	//Check backend connection
	*reply=getHypertext(SERVER_OSIR_HOME "/client-interface/echo.php?arg=OK");
	printf("Connection Status(OSIR):\t%s\n",*reply);
	if(strcmp(*reply,"OK"))exit(0);
	
	//Run backend test
	printf("\nServer Unit Test Results:\n");
	*reply=getHypertext(SERVER_OSIR_HOME "/client-interface/test.php?no_html");
	printf("%s",*reply);
	
	

	//free(reply);
	printf("\n");
	return 0;
}
