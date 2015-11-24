@echo off

REM set default C compiler
set c_compiler=tcc

REM build playground files
mkdir playground
%c_compiler% source\hello.c
move hello.exe playground\hello.exe
echo We dont need no education > playground\dummy.txt

REM build tests
echo Building client_unit.c
%c_compiler% source\client_unit.c  ..\client\common\clonelib.c ..\client\common\os.c ..\client\common\weakcrypt.c ..\client\common\thirdparty\b64.c ..\client\common\web.c -lws2_32
echo Building server_unit.c
%c_compiler% source\server_unit.c ..\client\common\web.c -lws2_32
