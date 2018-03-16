CRIPTIM
========================

Команды
--------------
Создание админа
 
`php bin/console fos:user:create admin`

`php bin/console fos:user:promote {admin_username} ROLE_ADMIN`

Обновление схемы бд

`php bin/console doctrine:schema:update --force`

отправка писем:

`php bin/console swiftmailer:spool:send --env=prod`

1. npm install - install packages from packages.json list
2. npm install -g bower - install vendor components from bower.json list
3. bower install
4. npm install -g gulp - install gulp
5. gulp build - to get project build once
6. gulp serve - to add watcher for src files.


If you are using docker-compose:


npm install
docker-compose run node bash
cd /criptim
rm -rf node_modules/* bower_components/* build/*
$npm install
$node ./node_modules/.bin/bower install --allow-root 
$node ./node_modules/.bin/gulp build --gulpfile fintobit-gulpfile
