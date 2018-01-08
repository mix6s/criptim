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
