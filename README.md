[![CircleCI](https://circleci.com/gh/Alsciende/fiveringsdb.svg?style=svg)](https://circleci.com/gh/Alsciende/fiveringsdb)

fiveringsdb
===========
A deckbuilder for **Legend of the Five Rings LCG**

## Installation

### Prerequisites

- php 7.x and various extensions (see composer.json)
- mysql (or compatible)
- git
- composer
- node 6.x (currently, the Ubuntu 16 official repo has node 4.x)
- npm

### Installation

- Create a database in MySQL: `mysql -uroot -p -e "create database fiveringsdb"`
- Clone the code repository: `git clone https://github.com/Alsciende/fiveringsdb.git`
- Clone the data repository: `git clone https://github.com/Alsciende/fiveringsdb-data data`
- Go to the code repository: `cd fiveringsdb`

### Back-end

- Install the vendors: `composer install`
- Create the schema and fixtures: `./reset-env dev`
- Run the Symfony server: `bin/console server:start localhost:8642`

### Images (optional)

- Symlink to a folder containing the card images: `ln -s /path/to/card/images web/bundles/card_images`

### Front-end

- Go to the frond-end project: `cd vue`
- Install the vendors: `npm install --no-optional`
- Export the API URL: `export FIVERINGSDB_API_URL=http://localhost:8642/app_dev.php`
- (optional) Export the card images folder URL: `export FIVERINGSDB_IMG_URL=http://localhost:8642/bundles/card_images`
- Run the Webpack server: `npm run dev`

### Tests

#### Back-end

```bash
./reset-env test
./vendor/phpunit/phpunit/phpunit
```

#### Front-end

Soon...

### Code Quality

#### Back-end

Soon...

#### Front-end

```bash
cd vue
./node_modules/.bin/eslint src/
```
