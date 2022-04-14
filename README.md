This is a basic API built with [Laravel](https://laravel.com) and runs on [Docker](https://docker.com). 

## Installing

Clone or unzip this repo. Then run:

    sail up

*Important note*: If you don't have Laravel's `sail` (https://laravel.com/docs/9.x/sail) on your local, you need to run  `composer install` manually. Or, you can run the next command to execute `composer install` on a docker container. This is needed only once to generate vendor folder, in case of your local PHP version is older den 8.1.

    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v $(pwd):/var/www/html \
        -w /var/www/html \
        laravelsail/php81-composer:latest \
        composer install --ignore-platform-reqs

## Routes

### GET `/api/cheapAndExpensive`
Most expensive and cheapest beer per litre.

    curl http://localhost/api/cheapAndExpensive?url=EXTERNAL_JSON_URL

### GET `/api/byPrice`
Which beers cost exactly €17.99?

    curl http://localhost/api/byPrice?price=17.99&url=EXTERNAL_JSON_URL

### GET `/api/mostBottles`
Which one product comes in the most bottles?

    curl http://localhost/api/mostBottles?url=EXTERNAL_JSON_URL

### GET `/api/all`
Combined results of other routes

    curl http://localhost/api/all?url=EXTERNAL_JSON_URL
