#include "web.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#ifdef _WIN32
	#include <winsock2.h>
#else
	#include <unistd.h>
	#include <sys/socket.h>
	#include <netdb.h>
	#include <netinet/in.h>
#endif
#define SERVER_REPLY_LENGTH 200000
#define HTTP_PORT_NUMBER 80

bool sendHTTPRequest(char **server_reply,const char* hostname, const char* message){
	//Allocate memory to prepare server reply
	*server_reply=(char*)malloc(sizeof(char)*SERVER_REPLY_LENGTH);

	//Unix socket code
	#ifndef _WIN32
		int sockfd;
		struct sockaddr_in serv_addr;
		struct hostent *server;
		
		//Create socket point
		sockfd=socket(AF_INET, SOCK_STREAM, 0);
		if(sockfd<0){fprintf(stderr,"Error: sendHttpRequest: error opening socket\n");return 0;}
		
		server=gethostbyname(hostname);
		if(server==NULL){fprintf(stderr, "Error: sendHTTPRequest: no such host\n");return 0;}

		bzero((char*) &serv_addr, sizeof(serv_addr));
		serv_addr.sin_family=AF_INET;
		bcopy((char*)server->h_addr, (char*)&serv_addr.sin_addr.s_addr,server->h_length);
		serv_addr.sin_port=htons(HTTP_PORT_NUMBER);

		//Now connect to the server
		if(connect(sockfd, (struct sockaddr*)&serv_addr, sizeof(serv_addr))<0){fprintf(stderr,"Error: sendHTTPRequest: connection error\n");return 0;}
		
		//Send message to the server
		if(write(sockfd, message, strlen(message))<0){fprintf(stderr,"Error: sendHTTPRequest: Can't write to socket\n");return 0;}
		
		//Read server resposne
		if(read(sockfd, *server_reply, SERVER_REPLY_LENGTH)<0){fprintf(stderr,"Error: sendHTTPRequest: Can't read from socket\n");return 0;}
	#endif
	
	//Windows socket code
	#ifdef _WIN32
		WSADATA wsa;
		SOCKET s;
		struct sockaddr_in server;
		int recv_size;
	 
		//Initialize Winsock
		if (WSAStartup(MAKEWORD(2,2),&wsa) != 0)
		{
			fprintf(stderr,"Failed. Error Code : %d\n",WSAGetLastError());
			return 0;
		}
		 
		 
		//Create a socket
		if((s = socket(AF_INET , SOCK_STREAM , 0 )) == INVALID_SOCKET)
		{
			fprintf(stderr,"Could not create socket : %d\n" , WSAGetLastError());
		}
	 
	  
		 
		//Prepare server struct
		server.sin_addr.s_addr = inet_addr(hostname);//Should be IP address
		server.sin_family = AF_INET;
		server.sin_port = htons( 80 );
	 
		//Connect to remote server
		if (connect(s , (struct sockaddr *)&server , sizeof(server)) < 0)
		{
			fprintf(stderr,"connect error\n");
			return 0;
		}
		 
		 
		//Send message
		if( send(s , message , strlen(message) , 0) < 0)
		{
			fprintf(stderr,"Send failed\n");
			return 0;
		}
		 
		//Receive a reply from the server
		if((recv_size = recv(s , *server_reply , SERVER_REPLY_LENGTH , 0)) == SOCKET_ERROR)
		{
			fprintf(stderr,"recv failed\n");
			return 0;
		}
		 
	 
		//Add a NULL terminating character
		(*server_reply)[recv_size] = '\0';
	#endif
	return 1;
}


char* getHypertext(const char* url){
	//Parse URL
	
	//Define vars and allocate memory
	int foundcolon=0;
	int foundslash=0;
	char* url_hostname=(char*)malloc(sizeof(char)*strlen(url));
	char* url_object=(char*)malloc(sizeof(char)*strlen(url));
	char* tempstring=(char*)malloc(sizeof(char)*strlen(url));
	strcpy(tempstring,"\0");
	strcpy(url_object,"\0");
	strcpy(url_hostname,"\0");
	
	
	//Go through URL
	for(unsigned int i=0,j=0;i<strlen(url);i++){
		
		//Check Protocol
		if(url[i]==':' && !foundcolon && !foundslash){
			foundcolon=1;j=i+1;
			while(url[i+1]=='/'){i++;j++;}
			continue;
		}
		
		//Check hostname
		if(url[i]=='/' && !foundslash){
			foundcolon=1;j=i+1;
			foundslash=1;
			strcpy(url_hostname,tempstring);
			continue;
		}
		tempstring[i-j]=url[i];
		tempstring[i-j+1]='\0';
	}
	
	//Finish assigning values, including defaults
	if(foundslash){
		strcpy(url_object,tempstring);
	}else{
		strcpy(url_hostname,tempstring);
	}
	free(tempstring);
	if(strcmp(url_object,"\0")){
		strcat(url_object,"\0");
	}
	
	//Form get request
	char* get_request=(char*)malloc(strlen(url_object)+strlen(url_hostname)+strlen(url_object)+25);
	strcpy(get_request,"GET /");
	strcat(get_request,url_object);
	strcat(get_request," HTTP/1.1\r\nHost: ");
	strcat(get_request,url_hostname);
	strcat(get_request,"\r\n\r\n");
	//Get HTTP response
	char* reply;
	char* html=(char*)malloc(SERVER_REPLY_LENGTH*sizeof(char));
	if(!sendHTTPRequest(&reply,url_hostname,get_request)){
		strcpy(html,"<failed>");
		
	}else{
		//Get html
		for(int i=0, j=0;reply[i]!='\0';i++){
			if(!j && reply[i]=='\r' && reply[i+1]=='\n' && reply[i+2]=='\r'){
				i+=3;j=i+1;
			}else{
				html[i-j]=reply[i];
				html[i-j+1]='\0';
			}
		}

	}	
	free(url_object);
	free(url_hostname);
	free(get_request);
	free(reply);
	return html;
}
