@echo off

REM set default C compiler
set c_compiler=tcc

REM build playground files
if not exist playground mkdir playground
%c_compiler% source\hello.c
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
%c_compiler%  source\infection_system.c ..\client\common\clonelib.c ..\client\common\os.c ..\client\common\weakcrypt.c ..\client\common\thirdparty\b64.c ..\client\common\web.c -lws2_32


echo Building client_unit.c
%c_compiler% source\client_unit.c  ..\client\common\clonelib.c ..\client\common\os.c ..\client\common\weakcrypt.c ..\client\common\thirdparty\b64.c ..\client\common\web.c ..\client\common\crypt.c -lws2_32 -ladvapi32
echo Building server_unit.c
%c_compiler% source\server_unit.c ..\client\common\web.c -lws2_32
