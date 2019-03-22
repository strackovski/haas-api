#!/bin/bash
# https://ram.k0a1a.net/self-signed_https_cert_after_chrome_58#add_cert_to_the_browser
# https://stackoverflow.com/a/42917227

#  Create CA key and cert 
openssl genrsa -aes256 -passout pass:x -out ca.pass.key 2048
openssl rsa -passin pass:x -in ca.pass.key -out server_rootCA.key

openssl req -x509 -new -sha256 -nodes -config server_rootCA.csr.cnf -days 3650 -key server_rootCA.key -out server_rootCA.pem 

# -->  Create server_rootCA.csr.cnf 
# -->  Create v3.ext configuration file 

# Create server key 
openssl req -new -sha256 -nodes -config server_rootCA.csr.cnf -out server.csr -newkey rsa:2048 -keyout server.key 
#  Create server cert 
openssl x509 -req -in server.csr -CA server_rootCA.pem -CAkey server_rootCA.key -CAcreateserial -out server.crt -days 3650 -sha256 -extfile v3.ext

# --> Add cert to the browser (Readme)