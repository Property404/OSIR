#!/usr/bin/env python
import base64
from os import urandom
from sys import argv
_XOR_KEY_SIZE=8

#xor two strings
def xor_str(s1,s2):
	print([a for a in s2]);
	return bytearray(s1[i]^s2[i%_XOR_KEY_SIZE] for i in range(len(s1)))


#xor msg with random key, encode in b64
def encrypt(msg):
	key=urandom(_XOR_KEY_SIZE);
	return (base64.b64encode((key+xor_str(msg,key))))+b"=";
	

#Undo encrypt, for testing
def decrypt(msg):
	msg=base64.b64decode(msg)
	return xor_str(bytearray(i for i in msg[_XOR_KEY_SIZE::]),msg[0:_XOR_KEY_SIZE]);


#Make encrypted file
def encrypt_file(fname,oname):
	fp=open(fname,"rb");
	ftext=fp.read();
	fp.close();
	fp=open(oname,"wb");
	fp.write(bytearray(i for i in encrypt(ftext)));
	fp.close();

def decrypt_file(fname,oname):
	fp=open(fname,"rb");
	ftext=fp.read();
	fp.close();
	fp=open(oname,"wb");
	fp.write(bytearray(i for i in decrypt(ftext)));
	fp.close();


if __name__=="__main__":
	usage="Usage: xcrypt.py [-rd] infile outfile"
	if(len(argv)<3):
		print(usage);
	elif(argv[1]=='-d'):
		if(len(argv)<4):
			print(usage);	
		decrypt_file(argv[2],argv[3]);
	elif(argv[1]=='-r'):
		if(len(argv)<4):
			print(usage);
		fp=open(argv[2],"r");
		ftext=fp.read();
		fp.close();
		fp=open(argv[3],"wb");
		fp.write(base64.b64decode(ftext));
		fp.close();		
	else:
		encrypt_file(argv[1],argv[2]);
	
	