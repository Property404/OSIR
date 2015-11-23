/*
	Outputs first few lines of Hello
	Goodbye by the Beatles using as
	few characters as possible, while
	still being valid ANSI C & C++.
	
	This code (not the output) is
	hereby placed into the public
	domain by the author, Dagan
	Martinez.
*/
#include <stdio.h>
#define i (j>1&&j<9||j>11?j-2:0)
#define a j==9?
int main(){int j=0;for(;j<18;j++)printf(i%3>1?"I don't know why ":i%3?"Hello hello\n":"%s say %s%s %s say %s\n",a"\nI":i?"you":"You",!j?"yes":j==1?"stop":a"high":j==10?"why":"goodbye",i+!j||a",":" and",a"you":"I",!j?"no":j==1?"go go go, oh no\n":a"low":j==10?"I don't know, oh no\n":"hello");return 0;}
