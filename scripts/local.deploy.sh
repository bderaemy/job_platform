#!/usr/bin/env sh
#set -e

#/**************************************/
#/*       LOCAL DEPLOYMENT ONLY        */
#/*                                    */
#/**************************************/


DRUPAL_DATABASE_NAME="drupal"
DRUPAL_DATABASE_PASSWORD="drupal_local"

DBDUMP_DIR="website-data-dump/db"
DBDUMP="${DBDUMP_DIR}/$(ls -t $DBDUMP_DIR | head -n1)"
export LOCAL_IP_ADDRESS=$(hostname -I | awk '{print $1;}')
export HOST_USER_ID=$(id -u)
export HOST_GROUP_ID=$(id -g)
DOCKER_PROJECT=$(basename "$PWD")
CONTAINER_MYSQL="${DOCKER_PROJECT}_mysql_1"
CONTAINER_DRUPAL="${DOCKER_PROJECT}_drupal_1"

printf "===========================\n Jobs demo Website Local Deployment\n===========================\n"
printf "===========================\n"

DOCKER_TAG="${DOCKER_TAG:=$(composer config -f app/composer.json name):latest}"

#docker build -t $DOCKER_TAG .

docker-compose -p $DOCKER_PROJECT up -d
#sleep 6


#printf "===========================\n"
#printf "===========================\nUpdate DB  \n===========================\n"
#docker exec -it $CONTAINER_DRUPAL drush updb --yes
#printf "===========================\nImport the config \n===========================\n"
#docker exec -it $CONTAINER_DRUPAL drush cim --yes
#printf "===========================\nClear cache \n===========================\n"
#docker exec -it $CONTAINER_DRUPAL drush cr --yes
