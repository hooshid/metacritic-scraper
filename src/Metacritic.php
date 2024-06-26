<?php

namespace Hooshid\MetacriticScraper;

use Exception;
use Hooshid\MetacriticScraper\Base\Base;
use voku\helper\HtmlDomParser;

class Metacritic extends Base
{

    /**
     * Search on metacritic
     *
     * @param $search
     * @param int $page
     * @param string $type
     * @param string $sort
     * @return array|string
     */
    public function search($search, int $page = 0, string $type = 'all', string $sort = 'relevancy')
    {
        if (!in_array($type, $this->searchTypes)) {
            return 'Type can be one of this: ' . implode(", ", $this->searchTypes);
        }

        if (!in_array($sort, $this->searchSorts)) {
            return 'Sort can be one of this: ' . implode(", ", $this->searchSorts);
        }

        $search = str_replace("/", " ", $search);
        //$response = $this->getContentPage(/results?sort=' . $sort);
        $mcoTypeId = null;
        if ($type == "movie") {
            $mcoTypeId = 2;
        } elseif ($type == "tv") {
            $mcoTypeId = 1;
        } elseif ($type == "person") {
            $mcoTypeId = 3;
        } elseif ($type == "game") {
            $mcoTypeId = 13;
        }
        $response = $this->getContentPage("https://internal-prod.apigee.fandom.net/v1/xapi/finder/metacritic/search/" . urlencode($search) . "/web?apiKey=1MOZgmNFxvmljaQR1X9KAij9Mo4xAY3u&offset=" . ($page * 100) . "&limit=100&componentName=search&componentDisplayName=Search&componentType=SearchResults&sortDirection=DESC&mcoTypeId=".$mcoTypeId);
        $html = HtmlDomParser::str_get_html($response);
        $last_page = 0;
        $output = [];
        try {
            $resultJson = json_decode($html);
            $last_page = $resultJson->links->last->meta->pageNum;
            foreach ($resultJson->data->items as $e) {
                $url = '/' . $this->getType($e->type) . '/' . $e->slug;
                $thumbnail = null;
                $output[] = [
                    'full_url' => $this->baseUrl . $url,
                    'url' => $url,
                    'url_slug' => $e->slug,
                    'title' => $this->cleanString($e->title),
                    'description' => $this->cleanString($e->description),
                    'thumbnail' => $thumbnail,
                    'year' => isset($e->premiereYear) & $e->premiereYear > 0 ? (int)$e->premiereYear : null,
                    'type' => $this->getType($e->type),
                    'meta_score' => isset($e->criticScoreSummary->score) ? (int)$e->criticScoreSummary->score : null,
                    'must_see' => (bool)$e->mustSee,
                    'score_class' => $this->getScoreClassByScore($e->criticScoreSummary->score),
                ];
            }
        } catch (Exception $exception) {
            $output = [];
        }

        return [
            'results' => $output,
            'paginate' => [
                'current_page' => $page,
                'last_page' => (int)$last_page,
                'per_page' => 100,
            ],
        ];
    }

    /**
     * Browse lists of metacritic
     *
     * @param $url
     * @param int $page
     * @return array
     */
    public function browse($url, int $page = 0): array
    {
        $url = str_replace("view=condensed", "view=detailed", $url);
        $url = str_replace("https://www.metacritic.com", "", $url);
        $sep = "?";
        if (stripos($url, "?") !== false) {
            $sep = "&";
        }

        $response = $this->getContentPage($this->baseUrl . $url . $sep . 'page=' . $page);
        $html = HtmlDomParser::str_get_html($response);
        $baseContent = $html->find('.title_bump', 0);
        $notFound = $baseContent->findOneOrFalse('.clamp-list');

        $output = [];
        if ($notFound !== false) {
            $i = 0;
            foreach ($baseContent->find('tr') as $e) {
                $url = $e->find('a.title', 0)->getAttribute('href');
                $thumbnail = $e->find('.clamp-image-wrap img', 0)->getAttribute('src');
                if (stripos($thumbnail, "http") === false) {
                    $thumbnail = null;
                }

                $itemInfo = $e->find('.clamp-details > span', 0)->text();
                if (preg_match("/(\d{4})/", $itemInfo, $matches)) {
                    $year = $matches[1];
                }

                $metaScore = $this->cleanString($e->find('.metascore_w', 0)->text());
                $userScore = $this->cleanString($e->find('.metascore_w.user', 0)->text());

                $title = $this->cleanString($e->find('a.title', 0)->text());
                $artist = $this->cleanString($e->find('.clamp-details .artist', 0)->text(), 'by ');
                $platform = $this->cleanString($e->find('.clamp-details .platform .data', 0)->text());
                preg_match("/(\w+)/", $url, $type);
                if (stripos($url, "trailers") !== false) {
                    $type[1] = "video";
                }

                if ($url and $title) {
                    $output[$i]['full_url'] = $this->baseUrl . $url;
                    $output[$i]['url'] = $url;
                    $output[$i]['url_slug'] = $this->afterLast($url);
                    $output[$i]['title'] = $this->cleanString($e->find('a.title', 0)->text());
                    $output[$i]['description'] = $this->cleanString($e->find('.summary', 0)->text());
                    $output[$i]['thumbnail'] = $thumbnail;
                    $output[$i]['year'] = isset($year) ? (int)$year : null;
                    $output[$i]['type'] = $this->getType($type[1]);
                    $output[$i]['number'] = (int)$e->find('.numbered', 0)->text();
                    $output[$i]['meta_score'] = isset($metaScore) ? (int)$metaScore : null;
                    $output[$i]['must_see'] = (bool)$e->findOneOrFalse('.mcmust');
                    $output[$i]['user_score'] = isset($userScore) ? (float)$userScore : null;
                    $output[$i]['score_class'] = $this->getScoreClass($e->find('.clamp-metascore .metascore_w', 0)->getAttribute('class'));
                    $output[$i]['user_score_class'] = $this->getScoreClass($e->find('.clamp-userscore .metascore_w', 0)->getAttribute('class'));

                    // for musics & albums
                    $output[$i]['artist'] = $artist ?? null;

                    // for games
                    $output[$i]['platform'] = $platform ?? null;

                    $i++;
                }
            }
        }

        return [
            'results' => $output,
            'paginate' => [
                'current_page' => $page,
                'last_page' => (int)$html->find('.pages .last_page .page_num', 0)->text(),
                'per_page' => 100,
            ],
        ];
    }

    /**
     * Extract data from movie, tv, music and game page
     *
     * @param $url
     * @return array
     */
    public function extract($url): array
    {
        $url = str_replace("https://www.metacritic.com", "", $url);
        $response = $this->getContentPage($this->baseUrl . $url);
        $html = HtmlDomParser::str_get_html($response);

        // extract type
        preg_match("/(\w+)/", $url, $typeMatch);
        $type = $this->getType($typeMatch[1]);
        $releaseYear = null;

        /***************************** Music *****************************/
        if ($type == 'music') {
            $title = $html->find('h1', 0)->text();
            $thumbnail = $html->find('img.product_image', 0)->getAttribute('src');
            if (stripos($thumbnail, "http") === false) {
                $thumbnail = null;
            }
            $itemInfo = $html->find('.summary_detail.release .data', 0)->text();
            if (preg_match("/(\d{4})/", $itemInfo, $matches)) {
                $releaseYear = $matches[1];
            }
            $metaScore = $html->find('.metascore_summary .metascore_w', 0)->text();
            $metaScoreVotesCount = $html->find('.metascore_summary .count a span', 0)->text();
            $mustSee = $html->findOneOrFalse('.must_play.product');
            $userScore = $html->find('.feature_userscore .metascore_w', 0)->text();
            $userScoreVotesCount = $html->find('.feature_userscore .count a', 0)->text();
            if ($html->findOneOrFalse('.product_summary .blurb_expanded')) {
                $summary = $html->find('.product_summary .blurb_expanded', 0)->text();
            } else {
                $summary = $html->find('.product_summary .data', 0)->text();
            }
            $genres = $html->find('.genres span span, .product_genre .data')->text();
        } else {
            $json = $this->jsonLD($response);
            $title = $this->cleanString($json->name);
            $genres = $json->genre;
            $thumbnail = $this->cleanString($json->image);
            if (stripos($thumbnail, "http") === false) {
                $thumbnail = null;
            }

            if (preg_match("/(\d{4})/", $this->cleanString($json->datePublished), $matches)) {
                $releaseYear = $matches[1];
            }

            $summary = $this->cleanString($json->description);
            $metaScore = $this->cleanString($json->aggregateRating->ratingValue);
            $metaScoreVotesCount = $this->cleanString($json->aggregateRating->reviewCount);
            $mustSee = $html->findOneOrFalse('.c-productScoreInfo_must');
            $userScore = $this->cleanString($html->find('.c-productHero_scoreInfo > .c-productScoreInfo .c-siteReviewScore span', 0)->text());
            $userScoreVotesCount = $this->cleanString($html->find('.c-productHero_scoreInfo > .c-productScoreInfo .c-productScoreInfo_reviewsTotal span', 0)->text());
        }

        $output = [];
        $output['full_url'] = $this->baseUrl . $url;
        $output['url'] = $url;
        $output['url_slug'] = $this->afterLast($url);
        $output['title'] = $title;
        $output['thumbnail'] = $thumbnail;
        $output['release_year'] = $releaseYear;
        $output['type'] = $type;
        $output['meta_score'] = isset($metaScore) ? (int)$metaScore : null;
        $output['meta_votes'] = isset($metaScoreVotesCount) ? $this->getNumbers($metaScoreVotesCount) : null;
        $output['user_score'] = isset($userScore) ? (float)$userScore : null;
        $output['user_votes'] = isset($userScoreVotesCount) ? $this->getNumbers($userScoreVotesCount) : null;
        $output['summary'] = $this->cleanString($summary, 'Summary:');
        $output['genres'] = $genres;

        if ($type == "movie" or $type == "tv") {
            $output['must_see'] = (bool)$mustSee;
            $output['rating'] = $this->cleanString($json->contentRating);

            $output['cast'] = [];
            if ($json->actor) {
                foreach ($json->actor as $e) {
                    $url = trim($e->url, '/');
                    $url = str_replace('https://www.metacritic.com', '', $url);
                    $url_slug = str_replace("/person/", "", $url);
                    $name = $e->name;

                    if (!empty($url_slug) and !empty($name)) {
                        $output['cast'][] = [
                            'name' => $this->cleanString($name),
                            'full_url' => $this->baseUrl . $this->cleanString($url),
                            'url_slug' => $this->cleanString($url_slug)
                        ];
                    }
                }
            }

            $output['director'] = [];
            if (isset($json->director)) {
                foreach ($json->director as $e) {
                    $url = trim($e->url, '/');
                    $url = str_replace('https://www.metacritic.com', '', $url);
                    $url_slug = str_replace("/person/", "", $url);
                    $name = $e->name;

                    if (!empty($url_slug) and !empty($name)) {
                        $output['director'][] = [
                            'name' => $this->cleanString($name),
                            'full_url' => $this->baseUrl . $this->cleanString($url),
                            'url_slug' => $this->cleanString($url_slug)
                        ];
                    }
                }
            }

            if (isset($json->creator)) {
                foreach ($json->creator as $e) {
                    $url = trim($e->url, '/');
                    $url = str_replace('https://www.metacritic.com', '', $url);
                    $url_slug = str_replace("/person/", "", $url);
                    $name = $e->name;

                    if (!empty($url_slug) and !empty($name)) {
                        $output['director'][] = [
                            'name' => $this->cleanString($name),
                            'full_url' => $this->baseUrl . $this->cleanString($url),
                            'url_slug' => $this->cleanString($url_slug)
                        ];
                    }
                }
            }
        } elseif ($type == "music") {
            $output['artist'] = $html->find('.product_artist a span', 0)->text();
        } elseif ($type == "game") {
            $output['must_play'] = (bool)$mustSee;
            $output['developers'] = $html->find('.c-gameDetails_Developer ul li')->text();
            $output['publishers'] = $html->find('.c-gameDetails_Distributor .g-outer-spacing-left-medium-fluid')->text();
            $output['genres'] = $html->find('.c-gameDetails_sectionContainer ul li span')->text();
        }

        return [
            'result' => $output,
            'error' => $this->cleanString($html->find('.error_title', 0)->text())
        ];
    }

    /**
     * Extract person data (actor, director and ...)
     *
     * @param $url
     * @return array
     */
    public function person($url): array
    {
        if (!strpos($url, 'person')) {
            $url = "/person/" . $url;
        }

        $response = $this->getContentPage($this->baseUrl . $url . "/?sort-options=date&filter=shows");
        $html = HtmlDomParser::str_get_html($response);
        $pageTitle = $this->cleanString($html->find('title', 0)->text());
        $pageTitle = trim(str_replace('- Metacritic', '', $pageTitle));
        $error = null;

        $output = [];
        $output['full_url'] = $this->baseUrl . $url;
        $output['url_slug'] = $this->afterLast($url);
        $output['name'] = null;
        $output['movies'] = [];
        $output['series'] = [];

        if ($pageTitle == 'Page Not Found'
            or strpos($pageTitle, 'Not Found') !== false
            or $html->findOneOrFalse('.c-error404')) {
            $error = 404;
        } else if ($pageTitle == 'Service Unavailable'
            or strpos($pageTitle, 'Service Unavailable') !== false
            or strpos($pageTitle, 'Error') !== false
            or $html->findOneOrFalse('.error_title')
            or $html->findOneOrFalse('.c-error503')) {
            $error = 503;
        } else {
            $output['name'] = $pageTitle;

            $response = $this->getContentPage("https://internal-prod.apigee.fandom.net/v1/xapi/people/metacritic/" . $output['url_slug'] . "/credits/web?apiKey=1MOZgmNFxvmljaQR1X9KAij9Mo4xAY3u&componentName=profile&componentDisplayName=Person%20Profile&componentType=Profile&productType=movie&sort=date");
            $html = HtmlDomParser::str_get_html($response);
            try {
                $scoreDetailsJson = json_decode($html);
                foreach ($scoreDetailsJson->data->items as $e) {
                    $output['movies'][] = [
                        'title' => $this->cleanString($e->product->title),
                        'url' => $this->baseUrl . rtrim($e->product->url, '/'),
                        'url_slug' => $this->afterLast(rtrim($e->product->url, '/')),
                        'year' => ((int)$e->product->releaseYear) ?: null
                    ];
                }
            } catch (Exception $exception) {
                $output['movies'] = [];
            }

            $response = $this->getContentPage("https://internal-prod.apigee.fandom.net/v1/xapi/people/metacritic/" . $output['url_slug'] . "/credits/web?apiKey=1MOZgmNFxvmljaQR1X9KAij9Mo4xAY3u&componentName=profile&componentDisplayName=Person%20Profile&componentType=Profile&productType=show&sort=date");
            $html = HtmlDomParser::str_get_html($response);
            try {
                $scoreDetailsJson = json_decode($html);
                foreach ($scoreDetailsJson->data->items as $e) {
                    $output['series'][] = [
                        'title' => $this->cleanString($e->product->title),
                        'url' => $this->baseUrl . rtrim($e->product->url, '/'),
                        'url_slug' => $this->afterLast(rtrim($e->product->url, '/')),
                        'year' => ((int)$e->product->releaseYear) ?: null
                    ];
                }
            } catch (Exception $exception) {
                $output['series'] = [];
            }
        }

        return [
            'result' => $output,
            'error' => $error
        ];
    }

}
