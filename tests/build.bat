@echo off
REM build playground files
set c_compiler=tcc
%c_compiler% source\hello.c
mkdir playground
move hello.exe playground\hello.exe
echo We dont need no education > playground\dummy.txt


REM build tests
%c_compiler% source\client_unit.c  ..\client\common\clonelib.c ..\client\common\os.c ..\client\common\weakcrypt.c ..\client\common\thirdparty\b64.c ..\client\common\web.c -lws2_32
