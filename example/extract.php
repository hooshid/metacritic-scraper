<?php

use Hooshid\MetacriticScraper\Metacritic;

require __DIR__ . "/../vendor/autoload.php";

if (empty($_GET["url"])) {
    header("Location: /example");
    exit;
}

$url = trim(strip_tags($_GET["url"]));

$metacritic = new Metacritic();
$extract = $metacritic->extract($url);
$result = $extract['result'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Extract</title>
    <link rel="stylesheet" href="/example/style.css">
</head>
<body>

<a href="/example" class="back-page">Go back</a>

<div class="container">
    <div class="boxed" style="max-width: 1300px;">
        <?php if ($result['title']) { ?>
            <h2 class="text-center pb-30">Extract data example: <?php echo $result['title']; ?></h2>
        <?php } ?>

        <div class="flex-container">
            <div class="col-25 menu-links">
                <div class="menu-links-title">Movies</div>
                <a href="extract.php?url=/movie/the-godfather">The Godfather (1972)</a>
                <a href="extract.php?url=/movie/the-matrix">The Matrix (1999)</a>
                <a href="extract.php?url=/movie/the-father">The Father (2021)</a>

                <div class="menu-links-title">TV</div>
                <a href="extract.php?url=/tv/game-of-thrones">Game of Thrones</a>
                <a href="extract.php?url=/tv/breaking-bad">Breaking Bad</a>

                <div class="menu-links-title">Music</div>
                <a href="extract.php?url=/music/happier-than-ever/billie-eilish">Billie Eilish - Happier than Ever</a>
                <a href="extract.php?url=/music/revival/selena-gomez">Selena Gomez - Revival</a>
                <a href="extract.php?url=/music/when-the-sun-goes-down-2011/selena-gomez-the-scene">Selena Gomez & the Scene - When the Sun Goes Down</a>

                <div class="menu-links-title">Game</div>
                <a href="extract.php?url=/game/xbox-series-x/microsoft-flight-simulator">Microsoft Flight Simulator - 2021</a>
                <a href="extract.php?url=/game/switch/ender-lilies-quietus-of-the-knights">ENDER LILIES: Quietus of the Knights - 2021</a>
            </div>

            <div class="col-75">
                <?php if ($extract['error']) { ?>
                    <div style="padding: 15px;background: #ff3737;border-radius: 5px;margin-bottom: 15px;color: #fff;"><?php echo $extract['error'];?></div>
                <?php } ?>
                <table class="table">
                    <!-- Main Url -->
                    <tr>
                        <td style="width: 140px;"><b>MetaCritic Full Url:</b></td>
                        <td>[<a href="<?php echo $result['full_url']; ?>"><?php echo $result['full_url']; ?></a>]</td>
                    </tr>

                    <!-- Title of page -->
                    <?php if ($result['title']) { ?>
                        <tr>
                            <td><b>Title:</b></td>
                            <td><?php echo $result['title']; ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Thumbnail -->
                    <?php if ($result['thumbnail']) { ?>
                        <tr>
                            <td><b>Thumbnail:</b></td>
                            <td><img src="<?php echo $result['thumbnail']; ?>" alt="<?php echo $result['title']; ?> thumbnail" style="max-width: 100px;"></td>
                        </tr>
                    <?php } ?>

                    <!-- Release Year -->
                    <?php if ($result['release_year']) { ?>
                        <tr>
                            <td><b>Release Year:</b></td>
                            <td><?php echo $result['release_year']; ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Meta Score & votes -->
                    <?php if ($result['meta_votes']) { ?>
                        <tr>
                            <td><b>Meta Score:</b></td>
                            <td>score: <?php echo $result['meta_score']; ?>, votes: <?php echo number_format($result['meta_votes']); ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Meta Score & votes -->
                    <?php if ($result['user_votes']) { ?>
                        <tr>
                            <td><b>User Score:</b></td>
                            <td>score: <?php echo $result['user_score']; ?>, votes: <?php echo number_format($result['user_votes']); ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Must See (on movie and tv) -->
                    <?php if (isset($result['must_see'])) {?>
                    <tr>
                        <td><b>Must See?</b></td>
                        <td><?php if ($result['must_see']) {echo "Yes";} else {echo "No";}?></td>
                    </tr>
                    <?php }?>

                    <!-- Must Play (on game) -->
                    <?php if (isset($result['must_play'])) {?>
                    <tr>
                        <td><b>Must Play?</b></td>
                        <td><?php if ($result['must_play']) {echo "Yes";} else {echo "No";}?></td>
                    </tr>
                    <?php }?>

                    <!-- Summary -->
                    <?php if ($result['summary']) { ?>
                        <tr>
                            <td><b>Summary:</b></td>
                            <td><?php echo $result['summary']; ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Genres -->
                    <?php if ($result['genres']) { ?>
                        <tr>
                            <td><b>Genres:</b></td>
                            <td><?php echo implode(', ', $result['genres']); ?></td>
                        </tr>
                    <?php } ?>

                    <!--------------------------------- For movie & tv --------------------------------->
                    <!-- Starring -->
                    <?php if(isset($result['starring']) and !empty($result['starring'])) { ?>
                        <tr>
                            <td><b>Starring:</b></td>
                            <td><?php echo implode(', ', $result['starring']); ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Director -->
                    <?php if (isset($result['director']) and !empty($result['director'])) { ?>
                        <tr>
                            <td><b>Director:</b></td>
                            <td><?php echo implode(' and ', $result['director']); ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Rating -->
                    <?php if (isset($result['rating'])) { ?>
                        <tr>
                            <td><b>Rating:</b></td>
                            <td><?php echo $result['rating']; ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Runtime -->
                    <?php if (isset($result['runtime'])) { ?>
                        <tr>
                            <td><b>Runtime:</b></td>
                            <td><?php echo $result['runtime']; ?></td>
                        </tr>
                    <?php } ?>

                    <!--------------------------------- For music --------------------------------->
                    <!-- Developers -->
                    <?php if (isset($result['artist'])) { ?>
                        <tr>
                            <td><b>Artist:</b></td>
                            <td><?php echo $result['artist']; ?></td>
                        </tr>
                    <?php } ?>

                    <!--------------------------------- For game --------------------------------->
                    <!-- Platform -->
                    <?php if (isset($result['platform']) and !empty($result['platform'])) { ?>
                        <tr>
                            <td><b>Platform:</b></td>
                            <td><?php echo $result['platform']; ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Developers -->
                    <?php if (isset($result['developers']) and !empty($result['developers'])) { ?>
                        <tr>
                            <td><b>Developers:</b></td>
                            <td><?php echo implode(', ', $result['developers']); ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Publishers -->
                    <?php if (isset($result['publishers']) and !empty($result['publishers'])) { ?>
                        <tr>
                            <td><b>Publishers:</b></td>
                            <td><?php echo implode(', ', $result['publishers']); ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Also on other platform -->
                    <?php if (isset($result['also_on']) and !empty($result['also_on'])) { ?>
                        <tr>
                            <td><b>Also on:</b></td>
                            <td class="menu-links">
                                <?php foreach ($result['also_on'] as $platform) { ?>
                                    <a href="extract.php?url=<?php echo $platform['url']; ?>"><?php echo $platform['title']; ?></a>
                                <?php }?>
                            </td>
                        </tr>
                    <?php } ?>

                    <!-- Cheat Url -->
                    <?php if (isset($result['cheat_url']) and !empty($result['cheat_url'])) { ?>
                        <tr>
                            <td><b>Cheat Url:</b></td>
                            <td><?php echo $result['cheat_url']; ?></td>
                        </tr>
                    <?php } ?>

                </table>
            </div>
        </div>
    </div>
</body>
</html>