<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <title>Metacritic</title>
    <link rel="stylesheet" href="/example/style.css">
</head>
<body>

<div class="container">
    <div class="boxed" style="max-width: 700px;">
        <h2 class="text-center pb-30">Search</h2>

        <form action="/example/search.php" method="get">
            <div class="form-group">
                <label for="search">Search:</label>
                <input class="form-field" type="text" id="search" name="search" maxlength="50" placeholder="Search...">
            </div>

            <div class="form-group">
                <label for="type">Type:</label>
                <select id="type" name="type" class="form-field">
                    <option value="all">All Items</option>
                    <option value="movie">Movies</option>
                    <option value="game">Games</option>
                    <option value="album">Albums</option>
                    <option value="tv">Tv Shows</option>
                    <option value="person">Person</option>
                    <option value="video">Trailers</option>
                    <option value="company">Companies</option>
                    <option value="story">Reports</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sort">Sort:</label>
                <select id="sort" name="sort" class="form-field">
                    <option value="relevancy">Relevancy</option>
                    <option value="score">Score</option>
                    <option value="recent">Most Recent</option>
                </select>
            </div>

            <div class="row">
                <input type="submit" value="Search">
            </div>
        </form>

    </div>

    <div class="boxed" style="max-width: 700px;">
        <h2 class="text-center pb-30">Examples</h2>

        <div class="menu-links">
            <a href="/example/extract.php?url=/movie/the-matrix">Movie - The Matrix (1999)</a>
            <a href="/example/extract.php?url=/movie/john-wick">Movie - John Wick (2014)</a>

            <a href="/example/extract.php?url=/tv/game-of-thrones">TV Series - Game of Thrones</a>
            <a href="/example/extract.php?url=/tv/house-of-the-dragon">TV Series - House of the Dragon</a>

            <a href="/example/person.php?url=tom-cruise">Person - Tom Cruise</a>
            <a href="/example/person.php?url=keanu-reeves">Person - Keanu Reeves</a>

            <a href="/example/browse.php?url=/browse/movies/score/metascore/all/filtered?sort=desc">Browse lists - Best Movies of All Time</a>
            <a href="/example/browse.php?url=/browse/tv/score/metascore/all/filtered?sort=desc">Browse lists - Best TV Shows of All Time</a>
            <a href="/example/browse.php?url=/browse/albums/score/metascore/all/filtered?sort=desc">Browse lists - Best Albums of All Time</a>
        </div>
    </div>
</div>

</body>
</html>
