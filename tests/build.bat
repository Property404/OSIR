@echo off

REM cleanup
del *.exe
del /F /Q playground\*
del /F /Q infection_test\*

REM set default C compiler
set c_compiler=tcc
set low_compiler=tcc

if "%1" == "gcc" (
	set c_compiler=gcc -s -std=c99 -Wall -Wextra -pedantic %2%
	set low_compiler=gcc
)
if "%1" == "g++" (
	set c_compiler=c++ -s -Wall -Wextra -pedantic %2%
	set low_compiler=c++
)


REM build playground files
if not exist playground mkdir playground
%low_compiler% source\hello.c -o hello.exe
move /y hello.exe playground\hello.exe > nul
echo We dont need no education > playground\dummy.txt

REM build system test directories
if not exist infection_test mkdir infection_test
cd infection_test
copy /y ..\playground\hello.exe hello.exe > nul
copy /y hello.exe+hello.exe hello_concat.exe > nul
copy /y hello_concat.exe+hello_concat.exe hello_concat2.exe > nul
copy /y hello_concat2.exe+hello_concat2.exe hello_concat3.exe > nul
cd ..

REM build tests
echo Building infection_system.c
%c_compiler%  source\infection_system.c ..\client\common\clonelib.c ..\client\common\os.c ..\client\common\weakcrypt.c ..\client\common\thirdparty\b64.c ..\client\common\web.c -lws2_32 -o infection_system.exe


echo Building client_unit.c
%c_compiler% source\client_unit.c  ..\client\common\ransomlib.c ..\client\common\clonelib.c ..\client\common\os.c ..\client\common\weakcrypt.c ..\client\common\thirdparty\b64.c ..\client\common\web.c ..\client\common\crypt.c -lws2_32 -ladvapi32 -o client_unit.exe


echo Building server_unit.c
%c_compiler% source\server_unit.c ..\client\common\web.c -lws2_32 -o server_unit.exe
