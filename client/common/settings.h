/*
  Build Settings for OSIR
  
  *SERVER_HOSTNAME must change
  to IP of remote server
*/
#ifndef HEADER_SETTINGS
	#define HEADER_SETTINGS 1
	
	//Replication Settings
	#define CROSS_PLATFORM_ON 1
	
	//Server IP address
	//#define SERVER_HOSTNAME "127.0.0.1"
	
	//OSIR root directory
	#define SERVER_OSIR_HOME "127.0.0.1/osir"
	
	//These should be the same size
	#define XBIN_WIN64 "/xbinaries/x_win64"
	#define XBIN_ELF64 "/xbinaries/x_elf64"
#endif

