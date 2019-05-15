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
- Clone the api repository: `git clone https://github.com/Alsciende/fiveringsdb-api.git api`
- Clone the data repository: `git clone https://github.com/Alsciende/fiveringsdb-data.git data`
- Clone the ui repository: `git clone https://github.com/Alsciende/fiveringsdb-ui.git ui`

- This should result in the following directory layout. If your layout looks different from that, the import of the data won't work straight away. 
    .
    ├── api                     
    ├── data                    
    └── ui

### Backend

- Go to the api project `cd api`
- Install the vendors: `composer install` (alternatively `composer update`)
- Create the schema: `bin/console doctrine:schema:create`
- Import the data: `bin/console app:data:import` 
- Run the Symfony server: `bin/console server:start localhost:8642`

### Images (optional)

- Symlink to a folder containing the card images: `ln -s /path/to/card/images web/bundles/card_images`

### Frontend

- Go to the frondend project: `cd ui`
- Copy environment file: `cp .env.example .env`
- Install the vendors: `npm install --no-optional`
- Export the API URL: `export FIVERINGSDB_API_URL=http://localhost:8642/app_dev.php/` (trailing slash mandatory)
- (optional) Export the card images folder URL: `export FIVERINGSDB_IMG_URL=http://localhost:8642/bundles/card_images/` (trailing slash mandatory)
- Run the Webpack server: `npm run dev`


