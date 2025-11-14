@echo off

git push origin main

:: Directorio donde está la app
set DIRECTORY=/root/webserver/web/dev.crm.setecem.com/api

:: Directorio donde está la app en el docker
set DIRECTORY_DOCKER=/var/www/site/dev.crm.setecem.com/api

:: Nombre del contenedor de docker
set CONTAINER=setecem-crm-api

:: Usuario@servidor remoto
set SERVER=root@sv5.setecem.com

:: Nombre del remote de git (default: origin)
set REMOTE=origin

:: Nombre de la rama remota
set BRANCH=main

:: Script composer
set COMPOSER_SCRIPT=php composer.phar update

git push %REMOTE% %BRANCH%


:: Ejecutamos todo el comando
ssh %SERVER% "cd %DIRECTORY% && git pull %REMOTE% %BRANCH% && docker exec -t %CONTAINER% /bin/bash -c 'cd %DIRECTORY_DOCKER% && %COMPOSER_SCRIPT% &&  php bin/doctrine orm:schema-tool:update --force' && chown -R www-data:www-data .";
