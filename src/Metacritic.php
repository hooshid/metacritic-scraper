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
     * @return array|string
     */
    public function search($search, int $page = 0, string $type = 'all')
    {
        if (!in_array($type, $this->searchTypes)) {
            return 'Type can be one of this: ' . implode(", ", $this->searchTypes);
        }

        $search = str_replace("/", " ", $search);

        $mcoTypeId = null;
        if ($type == "movie") {
            $mcoTypeId = 2;
        } elseif ($type == "tv") {
            $mcoTypeId = 1;
        } elseif ($type == "person") {
            $mcoTypeId = 3;
        }

        $response = $this->getContentPage("https://backend.metacritic.com/v1/xapi/finder/metacritic/search/" . urlencode($search) . "/web?apiKey=1MOZgmNFxvmljaQR1X9KAij9Mo4xAY3u&offset=" . ($page * 100) . "&limit=100&componentName=search&componentDisplayName=Search&componentType=SearchResults&sortDirection=DESC&mcoTypeId=" . $mcoTypeId);
        $html = HtmlDomParser::str_get_html($response);
        $last_page = 0;
        $output = [];
        try {
            $resultJson = json_decode($html);
            $last_page = $resultJson->links->last->meta->pageNum;
            foreach ($resultJson->data->items as $e) {
                $url = '/' . $this->getType($e->type) . '/' . $e->slug;
                $output[] = [
                    'full_url' => $this->baseUrl . $url,
                    'url' => $url,
                    'url_slug' => $e->slug,
                    'title' => $this->cleanString($e->title),
                    'thumbnail' => null,
                    'description' => $this->cleanString($e->description),
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
     * Extract data from movie or tv page
     *
     * @param $url
     * @return array
     */
    public function extract($url): array
    {
        $url = str_replace("https://www.metacritic.com", "", $url);
        $response = $this->getContentPage($this->baseUrl . $url);
        $html = HtmlDomParser::str_get_html($response);
        $pageTitle = $this->cleanString($html->find('title', 0)->text());
        $pageTitle = trim(str_replace('- Metacritic', '', $pageTitle));
        $error = null;

        $output = [];
        $output['full_url'] = $this->baseUrl . $url;
        $output['url'] = $url;
        $output['url_slug'] = $this->afterLast($url);

        if ($pageTitle == 'Page Not Found'
            or strpos($pageTitle, 'Not Found') !== false
            or $html->findOneOrFalse('.c-error404')) {
            $error = 404;
        } else if ($pageTitle == 'Service Unavailable'
            or strpos($pageTitle, 'Service Unavailable') !== false
            or $pageTitle == 'Error'
            or $html->findOneOrFalse('.error_title')
            or $html->findOneOrFalse('.c-error503')) {
            $error = 503;
        } else {
            // extract type
            preg_match("/(\w+)/", $url, $typeMatch);
            $type = $this->getType($typeMatch[1]);
            $releaseYear = null;

            $json = $this->jsonLD($response);
            $title = $this->cleanString($json->name);
            $thumbnail = null;
            $genres = $json->genre;

            if (preg_match("/(\d{4})/", $this->cleanString($json->datePublished), $matches)) {
                if ($matches[1] != "0000") {
                    $releaseYear = $matches[1];
                }
            }

            $summary = $this->cleanString($json->description);

            try {
                if (!empty($json->aggregateRating)) {
                    $metaScore = $this->cleanString($json->aggregateRating->ratingValue);
                    $metaScoreVotesCount = $this->cleanString($json->aggregateRating->reviewCount);
                }

                if (!empty($json->image)) {
                    $thumbnail = $this->cleanString($json->image);
                    if ($thumbnail and stripos($thumbnail, "http") === false) {
                        $thumbnail = null;
                    }
                }
            } catch (\Exception $e) {

            }

            $mustSee = $html->findOneOrFalse('.c-productScoreInfo_must');
            $userScore = $this->cleanString($html->find('.c-productHero_scoreInfo > .c-productScoreInfo .c-siteReviewScore span', 0)->text());
            $userScoreVotesCount = $this->cleanString($html->find('.c-productHero_scoreInfo > .c-productScoreInfo .c-productScoreInfo_reviewsTotal span', 0)->text());

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
        }

        return [
            'result' => $output,
            'error' => $error
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
        if (strpos($url, '/person') !== 0 || strpos($url, 'person') !== 0) {
            $url = "/person/" . $url;
        }

        $output = [];
        $output['full_url'] = $this->baseUrl . $url;
        $output['url_slug'] = $this->afterLast($url);
        $output['name'] = null;
        $output['movies'] = [];
        $output['series'] = [];
        $html = null;
        $error = null;
        $pageTitle = null;

        $response = $this->getContentPage($this->baseUrl . $url . "/?sort-options=date&filter=shows");
        if (!empty($response)) {
            $html = HtmlDomParser::str_get_html($response);
            if ($response != "403 Forbidden" and $html->findOneOrFalse('title')) {
                $pageTitle = $this->cleanString($html->find('title', 0)->text());
                $pageTitle = trim(str_replace('- Metacritic', '', $pageTitle));
            }
        }

        if (empty($pageTitle)) {
            $error = 500;
        } else if ($pageTitle == 'Page Not Found'
            or strpos($pageTitle, 'Not Found') !== false
            or $html->findOneOrFalse('.c-error404')) {
            $error = 404;
        } else if ($pageTitle == 'Service Unavailable'
            or trim($pageTitle) == ''
            or trim($pageTitle) == '- Metacritic'
            or trim($pageTitle) == 'Metacritic'
            or strpos($pageTitle, 'Service Unavailable') !== false
            or strpos($pageTitle, 'Error') !== false
            or $html->findOneOrFalse('.error_title')
            or $html->findOneOrFalse('.c-error503')) {
            $error = 503;
        } else {
            $output['name'] = $pageTitle;

            try {
                $response = $this->getContentPage("https://backend.metacritic.com/people/metacritic/" . $output['url_slug'] . "/credits/web?apiKey=1MOZgmNFxvmljaQR1X9KAij9Mo4xAY3u&componentName=profile&componentDisplayName=Person+Profile&componentType=Profile&productType=movie&sort=date");
                $html = HtmlDomParser::str_get_html($response);

                $scoreDetailsJson = json_decode($html);
                if (isset($scoreDetailsJson->data->items)) {
                    foreach ($scoreDetailsJson->data->items as $e) {
                        $output['movies'][] = [
                            'title' => $this->cleanString($e->product->title),
                            'url' => $this->baseUrl . rtrim($e->product->url, '/'),
                            'url_slug' => $this->afterLast(rtrim($e->product->url, '/')),
                            'year' => ((int)$e->product->releaseYear) ?: null
                        ];
                    }
                }
            } catch (Exception $exception) {
                $output['movies'] = [];
            }

            try {
                $response = $this->getContentPage("https://backend.metacritic.com/people/metacritic/" . $output['url_slug'] . "/credits/web?apiKey=1MOZgmNFxvmljaQR1X9KAij9Mo4xAY3u&componentName=profile&componentDisplayName=Person+Profile&componentType=Profile&productType=show&sort=date");
                $html = HtmlDomParser::str_get_html($response);

                $scoreDetailsJson = json_decode($html);
                if (isset($scoreDetailsJson->data->items)) {
                    foreach ($scoreDetailsJson->data->items as $e) {
                        $title = $this->cleanString($e->product->title);
                        preg_match('/\((\d{4})\)/', $title, $matches);
                        $titleYear = isset($matches[1]) ? (int)$matches[1] : null;
                        $title = trim(str_replace(" ($titleYear)","",$title));

                        $output['series'][] = [
                            'title' => $title,
                            'url' => $this->baseUrl . rtrim($e->product->url, '/'),
                            'url_slug' => $this->afterLast(rtrim($e->product->url, '/')),
                            'year' => ((int)$e->product->releaseYear) ?: null
                        ];
                    }
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
