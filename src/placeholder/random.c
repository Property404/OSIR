#include <stdio.h>
#include <time.h>
int trandint(const unsigned int m,const unsigned int seed){
	static unsigned int c=0;
	static unsigned int n=0;
	static unsigned int s=1234567890;
	s+=seed;
	n=(n^s)*s+(c++);
	return n%m;
}
void srandint(const unsigned int seed){
	trandint(1,seed);
}
int randint(const unsigned int m){
	return trandint(m,0);
}
int main(){
	srandint(time(NULL));
	for(int i=0;i<32;i++){
		printf("%c",'a'+randint(26));
	}
}