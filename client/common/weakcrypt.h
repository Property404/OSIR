#ifndef WEAKCRYPT_HEADER
#    define WEAKCRYPT_HEADER 1
#    include <stdbool.h>
//Connect to the server and decrypt weakly encrypted message
int decryptRemoteBytes(char **decrypted_msg, const char *url);

//Create a 2-byte hash
void hash16(char *hash, const char* input, const int length);
#endif