#!C:\Python34\python.exe -u
#!/usr/bin/env python
import cgi
import xcrypt

bins=0;
table=[["../binaries/elf64","../xbinaries/x_elf64"],["../binaries/win64.exe","../xbinaries/x_win64"]]

for plain,encrypted in table:
	try:
		xcrypt.encrypt_file(plain,encrypted);
		bins+=1;
	except FileNotFoundError:
		pass

print("Location: ./?op=manbin&note=Binaries%20Updated%3Cbr%3E{0}%20binar{1}%20encrypted%3Cp%3E".format(bins,("y" if bins==1 else "ies")));
print("Content-type: text/html\n")
