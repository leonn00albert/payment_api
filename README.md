Module 4 Project - Movie details API

Develop a CRUD REST Movie details API using the Slim PHP framework. The API should adhere to RESTful principles, utilize Composer for package management, and integrate Swagger for API 


## Requirements

1. Memcache 
2. MariaDB 

## Setup 
1. Setup your env file 

```bash
DRIVER=
HOST=
DATABASE=
USERNAME=
PASSWORD=
ENVIRONMENT=
TMDB_ACCESS_TOKEN=
MEMCACHE_HOST=
MEMCACHE_PORT=
```

use the seed.sql file to seed your db  


## Usage 

You can register  your email to get an API to access the v1 routes API docs a can be found at /docs

http header :
Content-Type : application/json
api_key : Your API KEY

```



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

