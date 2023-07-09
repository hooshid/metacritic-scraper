# Metacritic Scraper

<a href="https://github.com/hooshid/metacritic-scraper/actions"><img src="https://github.com/hooshid/metacritic-scraper/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/hooshid/metacritic-scraper"><img src="https://img.shields.io/packagist/dt/hooshid/metacritic-scraper" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/hooshid/metacritic-scraper"><img src="https://img.shields.io/packagist/v/hooshid/metacritic-scraper" alt="Latest Stable Version"></a>
<a href="LICENSE.md"><img src="https://img.shields.io/packagist/l/hooshid/metacritic-scraper" alt="License"></a>


Using this Metacritic API, you are able to search, browse and extract data of movies, tv series, musics and games on metacritic.com.

## Install
This library scrapes metacritic.com so changes their site can cause parts of this library to fail. You will probably need to update a few times a year.

### Requirements
* PHP >= 7.3
* PHP cURL extension

### Install via composer
``` bash
$ composer require hooshid/metacritic-scraper
```

## Run examples
The example gives you a quick demo to make sure everything's working, some sample code and lets you easily see some available data.

From the example folder in the root of this repository start up php's inbuilt webserver and browse to [http://localhost:8000]()

`php -S localhost:8000`


## Examples

### Get movie data
#### Movie: The Matrix (1999) / URL: https://www.metacritic.com/movie/the-matrix
``` php
$metacritic = new Hooshid\MetacriticScraper\Metacritic();
$extract = $metacritic->extract("/movie/the-matrix");
$result = $extract['result'];
$error = $extract['error'];

// get all available data as json
echo json_encode($extract);
```
in above example we first create a new obj from Metacritic() class, then we call extract method and give the metacritic.com url in first param.

if everything ok, result key filled and if not, the error key filled with error occurred


#### Tv Series: Game of Thrones (2011-2019) / URL: https://www.metacritic.com/tv/game-of-thrones
``` php
$metacritic = new Hooshid\MetacriticScraper\Metacritic();
$extract = $metacritic->extract("/tv/game-of-thrones");
$result = $extract['result'];
$error = $extract['error'];

if ($error) {
    echo $error;
} else {
    echo $result['type']; // type (movie, tv, game, person and ...)
    echo $result['title']; // movie/series title
    echo $result['thumbnail']; // Poster thumbnail
    echo $result['summary']; // Summary
    echo $result['release_year']; // Release year
    echo $result['must_see']; // Must see?
    
    echo $result['meta_score']; // Meta Score
    echo $result['meta_votes']; // Meta Votes
    echo $result['user_score']; // User Score
    echo number_format($result['user_votes']); // User Votes
}
```
you must always catch error first and get results.

### Search

``` php
$metacritic = new Hooshid\MetacriticScraper\Metacritic();
$result = $metacritic->search("it");

// get all available data as json
echo json_encode($result);
```
``` php
$metacritic = new Hooshid\MetacriticScraper\Metacritic();
$result = $metacritic->search("it", 0, "movie");

// output
{
  "results": [
    {
      "full_url": "https://www.metacritic.com/movie/it",
      "url": "/movie/it",
      "url_slug": "it",
      "title": "It",
      "description": "When children begin to disappear in the town of Derry, Maine, a group of young kids are faced with their biggest fears when they square off against an evil clown named Pennywise, whose history of...",
      "thumbnail": "https://static.metacritic.com/images/products/movies/8/ae92ae06d681d7eb2b0374d47787f3f8-78.jpg",
      "year": 2017,
      "type": "movie",
      "meta_score": 69,
      "must_see": false,
      "score_class": "positive"
    },
    {
        ...
    }
  ],
  "paginate": {
    "current_page": 0,
    "last_page": 16,
    "per_page": 10
  }
}
```
in above example we give 2 new param to method, $page must be integer as paginate.

$type by default return all, but you can specify this param to (all, movie, tv, game, album, music, person, video, company, story)

### Full examples
just open the example folder, we put all examples and methods demo for you in there!

## Related projects
* [IMDb Scraper](https://github.com/hooshid/imdb-scraper)
* [Rottentomatoes Scraper](https://github.com/hooshid/rottentomatoes-scraper)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
