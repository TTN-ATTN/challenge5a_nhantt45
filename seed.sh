#!/bin/bash

docker exec -it prog05-app php /var/www/html/seed.php 
docker exec -it prog05-app rm -rf /var/www/html/storage/uploads/*