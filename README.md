This is a basic API built with [Laravel](https://laravel.com) and runs on [Docker](https://docker.com). 

## Installing

Clone or unzip this repo. Then run:

    ./vendor/bin/sail up

## Routes

`GET /cheapAndExpensive?url=EXTERNAL_JSON_URL`

Most expensive and cheapest beer per litre.

`GET /byPrice?price=17.99&url=EXTERNAL_JSON_URL`

Which beers cost exactly €17.99?

`GET /mostBottles?url=EXTERNAL_JSON_URL`

Which one product comes in the most bottles?

`GET /all?url=EXTERNAL_JSON_URL`

Combined results of other routes