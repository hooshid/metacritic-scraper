<?php

use Hooshid\MetacriticScraper\Metacritic;

require __DIR__ . "/../vendor/autoload.php";

// if we have no search, go back to search page
if (empty($_GET["url"])) {
    header("Location: /example");
    exit;
}

$url = trim(strip_tags($_GET["url"]));
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 0;

$metacritic = new Metacritic();
$results = $metacritic->browse($url, $page);
$number = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Browse</title>
    <link rel="stylesheet" href="/example/style.css">
</head>
<body>

<a href="/example" class="back-page">Go back</a>

<div class="container">
    <div class="boxed" style="max-width: 1000px;">
        <h2 class="text-center pb-30">Browse lists (just 15 first result)</h2>

        <div class="flex-container">
            <div class="col-25 menu-links">
                <div class="menu-links-title">Movies</div>
                <a href="browse.php?url=/browse/movies/release-date/theaters/date">New Releases In Theaters</a>
                <a href="browse.php?url=/browse/movies/score/metascore/year/filtered">Best Movies This Year</a>
                <a href="browse.php?url=/browse/movies/score/metascore/all/filtered?sort=desc">Best Movies of All Time</a>

                <div class="menu-links-title">TV</div>
                <a href="browse.php?url=/browse/tv/release-date/new-series/date?view=detailed">New & Returning TV Shows</a>
                <a href="browse.php?url=/browse/tv/score/metascore/year/filtered?sort=desc&view=detailed">Best TV Shows This Year</a>
                <a href="browse.php?url=/browse/tv/score/metascore/all/filtered?sort=desc">Best TV Shows of All Time</a>

                <div class="menu-links-title">Music</div>
                <a href="browse.php?url=/browse/albums/release-date/new-releases/date">New Releases</a>
                <a href="browse.php?url=/browse/albums/score/metascore/year/filtered">Best Albums This Year</a>
                <a href="browse.php?url=/browse/albums/score/metascore/all/filtered?sort=desc">Best Albums of All Time</a>

                <div class="menu-links-title">Game</div>
                <a href="browse.php?url=/browse/games/score/metascore/year/all/filtered">Best Games This Year</a>
                <a href="browse.php?url=/browse/games/score/metascore/all/all/filtered">Best Games of All Time</a>
            </div>

            <div class="col-75">
                <ul class="search_results module">
                    <?php foreach ($results['results'] as $res) { ?>
                        <li class="result first_result">
                            <div class="result_wrap">

                                <?php if ($res['thumbnail']) { ?>
                                    <div class="result_thumbnail">
                                        <img src="<?php echo $res['thumbnail']; ?>" alt="<?php echo $res['title']; ?> thumbnail">
                                    </div>
                                <?php } ?>

                                <div class="basic_stats">
                                    <div class="main_stats">

                                        <?php if (!empty($res['meta_score']) or $res['meta_score'] === 0) { ?>
                                            <span class="metascore_w medium <?php echo $res['score_class']; ?>">
                                                <?php echo ($res['meta_score']) ?: 'tbd'; ?>
                                            </span>
                                        <?php } ?>

                                        <?php if (!empty($res['user_score']) or $res['user_score'] == 0) { ?>
                                            <span class="metascore_w user medium <?php echo $res['user_score_class']; ?>">
                                                <?php echo ($res['user_score']) ?: 'tbd'; ?>
                                            </span>
                                        <?php } ?>

                                        <?php if ($res['must_see']) { ?>
                                            <span class="must-see">
                                                <?php if ($res['type'] == "game") { ?>
                                                    <img src="https://www.metacritic.com/images/icons/mc-mustplay-sm.svg" alt="Must Play">
                                                <?php } else { ?>
                                                    <img src="https://www.metacritic.com/images/icons/mc-mustsee-sm.svg" alt="Must See">
                                                <?php } ?>
                                            </span>
                                        <?php } ?>

                                        <h3 class="product_title basic_stat">
                                            <?php if ($res['number']) {
                                                echo $res['number'] . ". ";
                                            } ?><a href="extract.php?url=<?php echo $res['url']; ?>"><?php echo $res['title']; ?></a> <span class="meta-link"><a href="<?php echo $res['full_url']; ?>" target="_blank">MC</a></span>
                                        </h3>
                                        <p>
                                            <?php echo $res['type']; ?>,
                                            <?php echo $res['year']; ?>
                                            <?php if ($res['type'] == "music" and $res['artist']) {
                                                echo ", by " . $res['artist'];
                                            } ?>
                                            <?php if ($res['type'] == "game" and $res['platform']) {
                                                echo ", Platform: " . $res['platform'];
                                            } ?>
                                        </p>
                                    </div>
                                </div>
                                <p class="deck basic_stat"><?php echo $res['description']; ?></p>
                            </div>
                        </li>
                        <?php $number++;
                        if ($number > 14) {
                            break;
                        }
                    } ?>
                </ul>

                <?php if ($results['paginate']['last_page']) { ?>
                    <form action="browse.php" method="get">
                        <input type="hidden" name="url" value="<?php echo $url; ?>">

                        <div class="form-group">
                            <label for="page">Page:</label>
                            <select id="page" name="page" onchange="this.form.submit();" class="form-field">
                                <?php for ($i = 0; $i < $results['paginate']['last_page']; $i++) { ?>
                                    <option value="<?php echo $i; ?>" <?php if ($i == $results['paginate']['current_page']) {
                                        echo "selected";
                                    } ?>><?php echo $i + 1; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </form>
                    <br>
                    <div style="clear: both;"></div>
                <?php } ?>

            </div>
        </div>
    </div>
</body>
</html>