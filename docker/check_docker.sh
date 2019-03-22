#!/bin/bash
## TODO :Check if ubuntu, then check if docker and docker-compose installed

## TODO
## check if any files are owned by root
## in syncdb script check for keys (idrsa)
## check userid in docker-compose file
## put zip of root files to www.petrol.si

#!/bin/bash
## TODO :Check if ubuntu, then check if docker and docker-compose installed
if [[ $(id -u) -eq 0 ]] ; then 
  echo "Please don't run this script as root" ; 
  exit 1;
fi

docker --version >> /dev/null 2>&1
if [ $? -ne 0 ];then
  echo "It seems that docker is not installed :"
  echo "Instructions UBUNTU : https://docs.docker.com/engine/installation/linux/docker-ce/ubuntu/ "
  echo "Instructions for MAC OS X : https://docs.docker.com/docker-for-mac/"
  exit 1;
fi

docker-compose --version >> /dev/null 2>&1
if [ $? -ne 0 ];then
  echo "It seems that docker-compose is not installed :"
  echo "Instructions (docker-compose) : https://docs.docker.com/compose/install/"
  echo "Instructions (docker-compose completion helper) : https://docs.docker.com/compose/completion/"
  exit 1;
fi

user_id=$(id -u)
if [ ! -f docker-compose.yml ]; then
  echo "I will now replace user id in php container!"
  sed "s/XYZuseridXYZ/$user_id/g" docker-compose.yml.example > docker-compose.yml
fi

## Create for nginx
KEY_SIZE=2048
if [ -d "./docker/resources/nginx/conf.d/ssl/" ]; then
  # Control will enter here if $DIRECTORY exists.
  if [ ! -f "./docker/resources/nginx/conf.d/ssl/dhparam2048.pem" ]; then
    # only generate if file does not exist
    openssl dhparam -out ./docker/resources/nginx/conf.d/ssl/dhparam${KEY_SIZE}.pem ${KEY_SIZE}
  fi  
fi