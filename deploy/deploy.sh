#!/bin/bash

## check_run checks :
# - if docker installed
# - if docker-compose installed


## First check if container is running - if it's running, stop!
CONTAINER_NAME="docker_deploy_container"
CONTAINER_EXISTS=`docker ps | grep $CONTAINER_NAME`

if [ -n "$CONTAINER_EXISTS" ]; then
  docker ps
  echo -e "\nOne shall not run two of the same containers at the same time!"
  echo -e "\n Try docker stop <container_id>"
  exit
fi

## TODO : Check this solution for forward keys
# https://gist.github.com/d11wtq/8699521
FILE_PRIVATE_KEY=~/.ssh/id_rsa
FILE_PUBLIC_KEY=~/.ssh/id_rsa.pub
FILE_GITHUB_AUTH=~/.gitconfig

## we presume that keys are valid
loop_keys_ok=true

if [ -f $FILE_PRIVATE_KEY ]; then
   echo "File $FILE_PRIVATE_KEY exists."
   let loop_keys_ok_keys_ok=false
else
   echo "File $FILE_PRIVATE_KEY does not exist."
   echo "It seems that your system is different."
   echo "Please contact info@nv3.eu for support."
   ls -ahl ~/.ssh/
fi

if [ -f $FILE_PUBLIC_KEY ]; then
   echo "File $FILE_PUBLIC_KEY exists."
   let loop_keys_ok=false
else
   echo "File $FILE_PUBLIC_KEY does not exist."
   echo "It seems that your system is different."
   echo "Please contact info@nv3.eu for support."
   ls -ahl ~/.ssh/
fi

while [ "$loop_keys_ok" == "true" ]
   do
     RED='\033[0;31m'
     NC='\033[0m' # No Color
   	 ## we are here because keys are not ok"
     echo -e "It seems that your PRIVATE key at path ( $FILE_PRIVATE_KEY ) does not exist or does not match $FILE_PUBLIC_KEY."
     echo -e "\nDisplaying the list of keys in your home folder : ${RED}"; ls -hl ~/.ssh/; echo -e "${NC}"
     echo -e "\nDisplaying your home folder (for easier copy/paste) : ${RED}$HOME${NC}/.ssh/"
     echo -e "\nPlease input ABSOLUTE path for your PRIVATE_KEY : " ; read FILE_PRIVATE_KEY
     echo -e "\nIt seems that your PUBLIC key at path ( $FILE_PUBLIC_KEY ) does not exist or does not match $FILE_PRIVATE_KEY."
     echo -e "\nPlease input ABSOLUTE path for your PUBLIC_KEY : " ; read FILE_PUBLIC_KEY
     key1=`cat $FILE_PUBLIC_KEY | cut -d' ' -f 2`
     key2=`ssh-keygen -y -f $FILE_PRIVATE_KEY | cut -d' ' -f 2`
     if [ "$key1" == "$key2" ]; then
     	echo -e "It seems that you entered valid private/public key pair. Proceeding."
     	let loop_keys_ok=true
     else
     	echo "Comparing $FILE_PRIVATE_KEY with $FILE_PUBLIC_KEY, we found a mismatch"
     	echo "Please try again, by entering different private/public key par."
     fi
   done

## we presume that github file exist
loop_github_auth_ok=true

if [ -f $FILE_GITHUB_AUTH ]; then
   echo "File $FILE_GITHUB_AUTH exists."
   GITHUB_AUTH=$(cat $FILE_GITHUB_AUTH)
   let loop_github_auth_ok=false
else
   echo "File $FILE_GITHUB_AUTH does not exist."
   echo "It seems that your system is different."
   echo "Please contact info@nv3.eu for support."
   ls -ahl ~/
fi

while [ "$loop_github_auth_ok" == "true" ]
   do
     ## we are here because github credentials are not ok"
     echo -e "It seems that your github config file at path ( $FILE_GITHUB_AUTH ) does not exist."
     echo -e "\nPlease input your slack nickname : " ; read input_name
     if [ -n "$input_name" ]; then
      GITHUB_AUTH="[user]\nname = $input_name"
      let loop_github_auth_ok=false
     fi
   done

echo Script name: $0 , $# arguments
user_id=$(id -u)
echo $user_id

docker build -t docker_deploy --build-arg arg_user_id="$user_id" --build-arg github_arg="$GITHUB_AUTH" --build-arg ssh_prv_key="$(cat $FILE_PRIVATE_KEY)" --build-arg ssh_pub_key="$(cat $FILE_PUBLIC_KEY)" .

folder=`pwd`
current_folder="$(dirname "$folder")"

echo $current_folder
mkdir -v .bundle

docker run -it --rm --name $CONTAINER_NAME -v $current_folder:/var/www/html -v $current_folder/deploy/.bundle:/usr/local/bundle docker_deploy
## TODO
#for ssh problems mount (with -v) the whole ~/.ssh folder into /root/.ssh folder