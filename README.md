# Slim Framework 4 Skeleton Application

[![Coverage Status](https://coveralls.io/repos/github/slimphp/Slim-Skeleton/badge.svg?branch=master)](https://coveralls.io/github/slimphp/Slim-Skeleton?branch=master)

Use this skeleton application to quickly setup and start working on a new Slim Framework 4 application. This application uses the latest Slim 4 with Slim PSR-7 implementation and PHP-DI container implementation. It also uses the Monolog logger.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application. You will require PHP 7.4 or newer.

```bash
composer create-project slim/slim-skeleton [my-app-name]
```

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writable.

To run the application in development, you can run these commands 

```bash
cd [my-app-name]
composer start
```

Or you can use `docker-compose` to run the app with `docker`, so you can run these commands:
```bash
cd [my-app-name]
docker-compose up -d
```
After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer test
```

That's it! Now go build something cool.



//routes
```bash
GET /v1/movies - get list of all existing movies
POST /v1/movies - add new movie to collection
PUT /v1/movies/{id} - updates movie by {id}
DELETE /v1/movies/{id} - deletes movie by {id}
PATCH /v1/movies/{id} - updates particular data of movie by {id}
GET /v1/movies/{numberPerPage} - get list of {numberPerPage} existing movies
GET /v1/movies/{numberPerPage}/sort/{fieldToSort} - get list of {numberPerPage} existing movies sorted by {fieldToSort}


```


//model
```json
{
 "uid": "1",
 "title": "Die Hard",
 "year": "1988",
 "released": "20 Jul 1988",
 "runtime": "132 min",
 "genre": "Action, Thriller",
 "director": "John McTiernan",
 "actors": "Bruce Willis, Alan Rickman, Bonnie Bedelia",
 "country": "United States",
 "poster": "https://m.media-amazon.com/images/M/MV5BZjRlNDUxZjAtOGQ4OC00OTNlLTgxNmQtYTBmMDgwZmNmNjkxXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_SX300.jpg",
 "imdb": "8.2",
 "type": "movie"
}

```



``` JSON
//JSON schema of response for (b - e):
{
 "status": "200/400/404/500",
 "message": "some kind of informative message bla bla bla…"
}

```
