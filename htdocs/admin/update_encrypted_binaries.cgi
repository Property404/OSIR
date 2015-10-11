#!C:\Python34\python.exe -u
#!/usr/bin/env python
import cgi
import xcrypt
xcrypt.encrypt_file("../binaries/elf64","../xbinaries/x_elf64");
xcrypt.encrypt_file("../binaries/win64.exe","../xbinaries/x_win64");
print("Location: index.php?note=Binaries Updated");
print("Content-type: text/html\n")