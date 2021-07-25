<?php

use Hooshid\MetacriticScraper\Metacritic;

require __DIR__ . "/../vendor/autoload.php";

// if we have no search, go back to search page
if (empty($_GET["search"])) {
    header("Location: /example");
    exit;
}

$search = trim(strip_tags($_GET["search"]));
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 0;
$type = isset($_GET["type"]) ? trim(strip_tags($_GET["type"])) : 'all';

$metacritic = new Metacritic();
$results = $metacritic->search($search, $page, $type);

function url($type): string
{
    global $search;
    return "/example/search.php?search=" . $search . "&type=" . $type;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Search - <?php echo strip_tags($_GET["search"]) ?></title>
    <link rel="stylesheet" href="/example/style.css">
</head>
<body>

<a href="/example" class="back-page">Go back</a>

<div class="container">
    <div class="boxed" style="max-width: 1000px;">
        <h2 class="text-center pb-30">Search results for <span><?php echo strip_tags($_GET["search"]) ?></span></h2>

        <div class="flex-container">
            <div class="col-25 menu-links">
                <a href="<?php echo url('all'); ?>">All Items</a>
                <a href="<?php echo url('movie'); ?>">Movies</a>
                <a href="<?php echo url('game'); ?>">Games</a>
                <a href="<?php echo url('album'); ?>">Albums</a>
                <a href="<?php echo url('tv'); ?>">Tv Shows</a>
                <a href="<?php echo url('person'); ?>">Person</a>
                <a href="<?php echo url('video'); ?>">Trailers</a>
                <a href="<?php echo url('company'); ?>">Companies</a>
                <a href="<?php echo url('story'); ?>">Reports</a>
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
                                            <a href="extract.php?url=<?php echo $res['url']; ?>"><?php echo $res['title']; ?></a> <span class="meta-link"><a href="<?php echo $res['full_url']; ?>"
                                                                                                                                                             target="_blank">MC</a></span>
                                        </h3>
                                        <p>
                                            <?php echo $res['type']; ?>
                                            <?php if ($res['type'] and $res['year']) {
                                                echo ", ";
                                            } ?>
                                            <?php echo $res['year']; ?>
                                        </p>
                                    </div>
                                </div>
                                <p class="deck basic_stat"><?php echo $res['description']; ?></p>
                            </div>
                        </li>
                    <?php } ?>
                </ul>

                <?php if ($results['paginate']['last_page']) { ?>
                    <form action="search.php" method="get">
                        <input type="hidden" name="search" value="<?php echo $search; ?>">
                        <input type="hidden" name="type" value="<?php echo $type; ?>">

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