**Setup Guide** *in progress*

1. [Install php and composer](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-18-04)

1. [Install the mongodb php driver](https://www.php.net/manual/ro/mongodb.installation.pecl.php)

1. Run `composer install`

1. Create a .env file in which you will put the sensible credentials (db, hosts, etc...) *i added this file to gitignore, please, NEVER EVER commit it* :)

1. To start the project for dev purposes execute `php -S localhost:8989` in the root folder of the project
****
*That's all, for now*