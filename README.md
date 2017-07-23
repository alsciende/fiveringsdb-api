[![CircleCI](https://circleci.com/gh/Alsciende/fiveringsdb.svg?style=svg)](https://circleci.com/gh/Alsciende/fiveringsdb)

fiveringsdb
===========
A deckbuilder for **Legend of the Five Rings LCG**

## Prerequisites

php 7.x, mysql, git, composer, node 6.x, npm

Install php DOM extension:

``` bash
sudo apt-get install php-xml php-zip php-mbstring
```

Create a database in MySQL:

``` bash
mysql -uroot -p -e "create database fiveringsdb"
```

## Checkout

Clone the code repository (this repository) to a location, e.g. `/var/www/fiveringsdb`. Also clone the data repository to e.g. `/home/toto/fiveringsdb-data`.

## Apache config

``` bash
sudo a2enmod rewrite
sudo cp fiveringsdb.conf.dist /etc/apache2/sites-available/fiveringsdb.conf
sudo a2ensite fiveringsdb.conf
sudo service apache2 reload
```

## Back-end

``` bash
export SYMFONY_ENV=prod
composer install --no-dev
./reset-env prod
```

Then [fix Symfony permissions](http://symfony.com/doc/current/setup/file_permissions.html).

## Images

``` bash
ln -s /path/to/card/images web/bundles/card_images
```

## Front-end

``` bash
cd vue
npm install
npm run build
```

## Tests

``` bash
./reset-env test
bin/phpunit
```

## Dev

``` bash
./reset-env dev
cd vue
npm run dev
```

# Coding Guidelines

## Status code vs Error message

When to response with a non-200 status code and when to return a response with `"success"=false` and an error message?

Use a status code when nothing can be done to change the result of the request. Example: `POST /decks/{id}/publish` is requested but `id` references a public deck: return 404. `POST /decks/{id}/publish` is requested but `id` references a deck of another user: return 403.

Use an error message to communicate what is wrong, when the user can fix the situation. Example: `POST /strains/{id}` is requested but the user has reached their quota for the number of decks: return an error message informing the user that their quota is reached.
