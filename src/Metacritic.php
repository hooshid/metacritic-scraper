<?php

namespace Hooshid\MetacriticScraper;

use Hooshid\MetacriticScraper\Base\Base;
use voku\helper\HtmlDomParser;

class Metacritic extends Base
{
    protected $baseUrl = 'https://www.metacritic.com';

    protected $searchTypes = ['all', 'movie', 'game', 'album', 'music', 'tv', 'person', 'video', 'company', 'story'];

    protected $searchSorts = ['relevancy', 'score', 'recent'];

    /**
     * Helper function for get meta score class
     *
     * @param $str
     * @return string
     */
    protected function getScoreClass($str): string
    {
        if (stripos($str, "positive") !== false) {
            return "positive";
        } elseif (stripos($str, "mixed") !== false) {
            return "mixed";
        } elseif (stripos($str, "negative") !== false) {
            return "negative";
        }

        return "tbd";
    }

    /**
     * get type of page or result
     *
     * @param $type
     * @return string|null
     */
    protected function getType($type): ?string
    {
        foreach ($this->searchTypes as $loop) {
            if ($loop == $type) {
                return $loop;
            }
        }

        return null;
    }

    /**
     * Search on metacritic
     *
     * @param $search
     * @param int $page
     * @param string $type
     * @param string $sort
     * @return array|string
     */
    public function search($search, $page = 0, $type = 'all', $sort = 'relevancy')
    {
        if (!in_array($type, $this->searchTypes)) {
            return 'Type can be one of this: ' . implode(", ", $this->searchTypes);
        }

        if (!in_array($sort, $this->searchSorts)) {
            return 'Sort can be one of this: ' . implode(", ", $this->searchSorts);
        }


        $response = $this->getContentPage($this->baseUrl . '/search/' . $type . '/' . urlencode($search) . '/results?sort=' . $sort . '&page=' . $page);
        $html = HtmlDomParser::str_get_html($response);
        $baseContent = $html->find('.module.search_results', 0);
        $notFound = $baseContent->findOneOrFalse('li.result');


        $output = [];
        if ($notFound !== false) {
            $i = 0;
            foreach ($baseContent->find('li.result') as $e) {
                $url = $e->find('a', 0)->getAttribute('href');
                $thumbnail = $e->find('.result_thumbnail img', 0)->getAttribute('src');
                if (stripos($thumbnail, "http") === false) {
                    $thumbnail = null;
                }

                $itemInfo = $e->find('.main_stats p', 0)->text();
                $itemInfo = strtolower($this->cleanString($itemInfo));
                $itemInfo = str_replace("tv show", "tv", $itemInfo);

                if (preg_match("/(\d{4})/", $itemInfo, $matches)) {
                    $year = (int)$matches[1];
                }

                $metaScore = $e->find('.main_stats .metascore_w', 0)->text();
                preg_match("/(\w+)/", $url, $type);
                if (stripos($url, "trailers") !== false) {
                    $type[1] = "video";
                }

                $output[$i]['full_url'] = $this->baseUrl . $url;
                $output[$i]['url'] = $url;
                $output[$i]['url_slug'] = $this->afterLast($url);
                $output[$i]['title'] = $this->cleanString($e->find('.product_title', 0)->text());
                $output[$i]['description'] = $this->cleanString($e->find('.deck', 0)->text());
                $output[$i]['thumbnail'] = $thumbnail;
                $output[$i]['year'] = $year ?? null;
                $output[$i]['type'] = $this->getType($type[1]);
                $output[$i]['meta_score'] = isset($metaScore) ? (int)$metaScore : null;
                $output[$i]['must_see'] = (bool)$e->findOneOrFalse('.main_stats .must-see');
                $output[$i]['score_class'] = $this->getScoreClass($e->find('.main_stats .metascore_w', 0)->getAttribute('class'));
                $i++;
            }
        }

        return [
            'results' => $output,
            'paginate' => [
                'current_page' => (int)$page,
                'last_page' => (int)$html->find('.pages .last_page .page_num', 0)->text(),
                'per_page' => 10,
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
    public function browse($url, $page = 0): array
    {
        $url = str_replace("view=condensed", "view=detailed", $url);
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

                $metaScore = $e->find('.metascore_w', 0)->text();
                $userScore = $e->find('.metascore_w.user', 0)->text();

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
                'current_page' => (int)$page,
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
        $response = $this->getContentPage($this->baseUrl . $url);
        $html = HtmlDomParser::str_get_html($response);

        // extract type
        preg_match("/(\w+)/", $url, $typeMatch);
        $type = $this->getType($typeMatch[1]);

        // extract title
        $title = $html->find('h1', 0)->text();

        // extract thumbnail
        if($type == "movie" or $type == "tv") {
            $thumbnail = $html->find('.summary_img', 0)->getAttribute('src');
        } else {
            $thumbnail = $html->find('img.product_image', 0)->getAttribute('src');
        }

        if (stripos($thumbnail, "http") === false) {
            $thumbnail = null;
        }

        // extract release year
        if($type == "movie" or $type == "tv") {
            $releaseYear = (int)$html->find('.product_page_title .release_year', 0)->text();
            if (empty($releaseYear)) {
                $itemInfo = $html->find('.details_section .release_date', 0)->text();
                if (preg_match("/(\d{4})/", $itemInfo, $matches)) {
                    $releaseYear = $matches[1];
                }
            }
        } elseif($type == "music") {
            $itemInfo = $html->find('.summary_detail.release .data', 0)->text();
            if (preg_match("/(\d{4})/", $itemInfo, $matches)) {
                $releaseYear = $matches[1];
            }
        } else {
            $itemInfo = $html->find('.release_data .data', 0)->text();
            if (preg_match("/(\d{4})/", $itemInfo, $matches)) {
                $releaseYear = $matches[1];
            }
        }

        // extract scores
        if($type == "movie" or $type == "tv") {
            $metaScore = $html->find('.summary_right .metascore_w', 0)->text();
            $metaScoreVotesCount = $html->find('.summary_left .based_on', 0)->text();
            $mustSee = $html->findOneOrFalse('.summary_right .must-see');
            $userScore = $html->find('.user_score_summary .metascore_w', 0)->text();
            $userScoreVotesCount = $html->find('.user_score_summary .score_description .based_on', 0)->text();
        } else {
            $metaScore = $html->find('.metascore_summary .metascore_w', 0)->text();
            $metaScoreVotesCount = $html->find('.metascore_summary .count a span', 0)->text();
            $mustSee = $html->findOneOrFalse('.must_play.product');
            $userScore = $html->find('.feature_userscore .metascore_w', 0)->text();
            $userScoreVotesCount = $html->find('.feature_userscore .count a', 0)->text();
        }

        // extract summary
        if($type == "movie" or $type == "tv") {
        if ($html->findOneOrFalse('.summary_deck .blurb_expanded')) {
            $summary = $html->find('.summary_deck .blurb_expanded', 0)->text();
        } else {
            $summary = $html->find('.summary_deck', 0)->text();
        }
        } else {
            if ($html->findOneOrFalse('.product_summary .blurb_expanded')) {
                $summary = $html->find('.product_summary .blurb_expanded', 0)->text();
            } else {
                $summary = $html->find('.product_summary .data', 0)->text();
            }
        }

        $output = [];
        $output['full_url'] = $this->baseUrl . $url;
        $output['url'] = $url;
        $output['url_slug'] = $this->afterLast($url);
        $output['title'] = $this->cleanString($title);
        $output['thumbnail'] = $thumbnail;
        $output['release_year'] = $releaseYear;
        $output['type'] = $type;
        $output['meta_score'] = isset($metaScore) ? (int)$metaScore : null;
        $output['meta_votes'] = isset($metaScoreVotesCount) ? $this->getNumbers($metaScoreVotesCount) : null;
        $output['user_score'] = isset($userScore) ? (float)$userScore : null;
        $output['user_votes'] = isset($userScoreVotesCount) ? $this->getNumbers($userScoreVotesCount) : null;
        $output['summary'] = $this->cleanString($summary, 'Summary:');
        if($type == "movie" or $type == "tv") {
            $output['must_see'] = (bool)$mustSee;
            $output['starring'] = $html->find('.summary_cast span a')->text();
            if ($output['type'] == "tv") {
                $output['director'] = $html->find('.creator a span')->text();
            } else {
                $output['director'] = $html->find('.director a span')->text();
            }

            $output['rating'] = $this->cleanString($html->find('.rating span', 1)->text());
            $output['runtime'] = $this->cleanString($html->find('.runtime span', 1)->text());
        }

        $output['genres'] = $html->find('.genres span span, .product_genre .data')->text();

        if($type == "music") {
            $output['artist'] = $html->find('.product_artist a span',0)->text();
        }

        if($type == "game"){
            $output['must_play'] = (bool)$mustSee;
            $output['developers'] = $html->find('li.developer a')->text();
            $output['publishers'] = $html->find('li.publisher a')->text();

            $i=0;
            foreach ($html->find('li.product_platforms a') as $element) {
                $output['also_on'][$i]['title'] = trim($element->plaintext);
                $output['also_on'][$i]['url'] = trim($element->href);
                $i++;
            }

            $output['cheat_url'] = $html->find('li.product_cheats a',0)->href;
            $output['platform'] = $html->find('.product_title .platform a',0)->text();
        }

        return [
            'result' => $output,
            'error' => $this->cleanString($html->find('.error_title', 0)->text())
        ];
    }
}
