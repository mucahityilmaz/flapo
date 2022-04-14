This is a basic API built with [Laravel](https://laravel.com) and runs on [Docker](https://docker.com). 

## Installing

Clone or unzip this repo. Then run:

    sail up

Note: If you don't have `sail` on your local, you may run `compsoer install` manually. Or, you can run the next command to execute `composer install` on a docker container. This is needed only once to generate vendor folder, in case of your local PHP version is older den 8.1.

    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v $(pwd):/var/www/html \
        -w /var/www/html \
        laravelsail/php81-composer:latest \
        composer install --ignore-platform-reqs

## Routes

`GET http://localhost/cheapAndExpensive?url=EXTERNAL_JSON_URL`

Most expensive and cheapest beer per litre.

`GET http://localhost/byPrice?price=17.99&url=EXTERNAL_JSON_URL`

Which beers cost exactly €17.99?

`GET http://localhost/mostBottles?url=EXTERNAL_JSON_URL`

Which one product comes in the most bottles?

`GET http://localhost/all?url=EXTERNAL_JSON_URL`

Combined results of other routes