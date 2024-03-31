<?php

use Hooshid\MetacriticScraper\Metacritic;

require __DIR__ . "/../vendor/autoload.php";

if (empty($_GET["url"])) {
    header("Location: /example");
    exit;
}

$url = trim(strip_tags($_GET["url"]));

$metacritic = new Metacritic();
$person = $metacritic->person($url);
$result = $person['result'];

if (isset($_GET["output"])) {
    header("Content-Type: application/json");
    echo json_encode($person);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Person</title>
    <link rel="stylesheet" href="/example/style.css">
</head>
<body>

<a href="/example" class="back-page">Go back</a>
<a href="/example/person.php?<?php echo http_build_query($_GET); ?>&output=json" class="output-json-link">JSON
    Format</a>

<div class="container">
    <div class="boxed" style="max-width: 1300px;">
        <?php if ($result['name']) { ?>
            <h2 class="text-center pb-30">Extract data example: <?php echo $result['name']; ?></h2>
        <?php } ?>

        <div class="flex-container">
            <div class="col-25 menu-links">
                <div class="menu-links-title">Links</div>
                <a href="person.php?url=tom-cruise">Tom Cruise</a>
                <a href="person.php?url=bryan-cranston">Bryan Cranston</a>
                <a href="person.php?url=keanu-reeves">Keanu Reeves</a>
                <a href="person.php?url=george-rr-martin">George R.R. Martin</a>
            </div>

            <div class="col-75">
                <?php if ($person['error']) { ?>
                    <div style="padding: 15px;background: #ff3737;border-radius: 5px;margin-bottom: 15px;color: #fff;"><?php echo $person['error']; ?></div>
                <?php } ?>
                <table class="table">
                    <!-- Main Url -->
                    <tr>
                        <td style="width: 140px;"><b>MetaCritic Full Url:</b></td>
                        <td>[<a href="<?php echo $result['full_url']; ?>"><?php echo $result['full_url']; ?></a>]</td>
                    </tr>

                    <!-- Name -->
                    <?php if ($result['name']) { ?>
                        <tr>
                            <td><b>Name:</b></td>
                            <td><?php echo $result['name']; ?></td>
                        </tr>
                    <?php } ?>

                    <!-- Movies -->
                    <?php if ($result['movies']) { ?>
                        <tr>
                            <td><b>Movies:</b></td>
                            <td class="menu-links">
                                <?php foreach ($result['movies'] as $movie) { ?>
                                    <a href="extract.php?url=<?php echo $movie['url']; ?>"><?php echo $movie['title']; ?>
                                        (<?php echo $movie['year']; ?>)</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                    <!-- Series -->
                    <?php if ($result['series']) { ?>
                        <tr>
                            <td><b>TV:</b></td>
                            <td class="menu-links">
                                <?php foreach ($result['series'] as $tv) { ?>
                                    <a href="extract.php?url=<?php echo $tv['url']; ?>"><?php echo $tv['title']; ?>
                                        (<?php echo $tv['year']; ?>)</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                </table>
            </div>
        </div>
    </div>
</body>
</html>