#!/bin/bash
if [ $# -eq 0 ]
  then
    echo "You are now in bash in a container."
	docker exec -it -u www-data haas-php /bin/bash
  else
    echo "Executing commmand $@ in a container."
	docker exec -it -u www-data haas-php "$@"
fi