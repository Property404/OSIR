/*
  Build Settings
*/
#ifndef HEADER_SETTINGS
	#define HEADER_SETTINGS 1
	
	//Replication Settings
	#define CROSS_PLATFORM_ON 1
	#define MAX_EXEC_SIZE 99000
	#define XOR_KEY_SIZE 8 //Do not change without changing xcrypt.py
	
	//Server IP address
	#define SERVER_HOSTNAME "127.0.0.1"
	
	//OSIR root directory
	#define SERVER_OSIR_HOME SERVER_HOSTNAME "/osir"
	
	//These should be the same size
	#define XBIN_WIN64 "/xbinaries/x_win64"
	#define XBIN_ELF64 "/xbinaries/x_elf64"
#endif

