#!/bin/sh
# Start the first process
php-fpm8.1 -D
status=$?
if [ $status -ne 0 ]; then
  echo "Failed to start php-fpm8.1: $status"
  exit $status
fi

cd /var/www/deltabook/current/ && chown -R :www-data .
cd /var/www/deltabook/current/ && chmod -R g+w .

cd /var/www/deltabook/current/ && php init --env=$ENV_TYPE --overwrite=All

cd /var/www/deltabook/current/ && composer install

#install for frontend
cd /var/www/deltabook/current/ && yarn install
cd /var/www/deltabook/current/ && nohup yarn run prod &
#
#install for admin
cd /var/www/deltabook/current/backend/ && yarn install
cd /var/www/deltabook/current/backend/ && yarn run prod &

while sleep 60; do
  ps aux |grep "php-fpm" |grep -q -v grep
  PROCESS_1_STATUS=$?
  # If the greps above find anything, they will exit with 0 status
  # If they are not both 0, then something is wrong
  if [ $PROCESS_1_STATUS -ne 0 ]; then
    echo "php down!"
    exit 1
  fi
done
