#!/bin/bash
#docker exec -it -u www-data haas-php /bin/bash

echo "Running composer self-update"
docker exec -u www-data haas-php composer self-update
if [ $# -eq 0 ]
  then
    echo "Running composer install"
    docker exec -u www-data haas-php composer install
  else
    echo "Running composer $@"
    docker exec -u www-data haas-php composer "$@"
fi