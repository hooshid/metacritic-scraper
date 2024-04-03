<?php


namespace Hooshid\MetacriticScraper\Base;


class Base
{
    protected $baseUrl = 'https://www.metacritic.com';

    protected $searchTypes = ['all', 'movie', 'game', 'album', 'music', 'tv', 'person', 'video', 'company', 'story'];

    protected $searchSorts = ['relevancy', 'score', 'recent'];

    /**
     * Get html content
     *
     * @param $url
     * @return bool|string
     */
    protected function getContentPage($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.4472.124 Safari/537.36");
        $page = curl_exec($ch);
        curl_close($ch);

        return $page;
    }

    /**
     * Clean string from html tags
     *
     * @param $str
     * @param null $remove
     * @return string|null
     */
    protected function cleanString($str, $remove = null): ?string
    {
        if (empty($str)) {
            return null;
        }
        if (!empty($remove)) {
            $str = str_replace($remove, "", $str);
        }

        $str = str_replace("&amp;", "&", $str);
        $str = str_replace("&nbsp;", " ", $str);
        $str = str_replace("   ", " ", $str);
        $str = str_replace("  ", " ", $str);
        $str = html_entity_decode($str);

        $str = trim(strip_tags($str));
        if (empty($str)) {
            return null;
        }

        return $str;
    }

    /**
     * get value after last specific char
     *
     * @param $str
     * @param string $needle
     * @return string
     */
    protected function afterLast($str, string $needle = '/'): string
    {
        return substr($str, strrpos($str, $needle) + 1);
    }

    /**
     * get value before last specific char
     *
     * @param $str
     * @param string $needle
     * @return string
     */
    protected function beforeLast($str, string $needle = '/'): string
    {
        $lastPos = strrpos($str, $needle);
        if ($lastPos === false) {
            return $str;
        }
        return substr($str, 0, $lastPos);
    }

    /**
     * extract numbers from string
     *
     * @param $str
     * @return int
     */
    protected function getNumbers($str): int
    {
        return (int)filter_var($str, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * @return mixed|null
     */
    protected function jsonLD($html)
    {
        if (empty($html)) return [];
        preg_match('#<script data-n-head="ssr" charset="UTF-8" type="application/ld\+json" data-hid="ld\+json">(.+?)</script>#ims', $html, $matches);
        //preg_match('#<script type="application/ld\+json">(.+?)</script>#ims', $html, $matches);
        if (empty($matches[1])) return [];
        return json_decode($matches[1]);
    }

    /**
     * get type of page or result
     *
     * @param $type
     * @return string|null
     */
    protected function getType($type): ?string
    {
        if ($type == "show") {
            return "tv";
        } else if ($type == "game-title") {
            return "game";
        } else if ($type == "trailers") {
            return "video";
        }

        foreach ($this->searchTypes as $loop) {
            if ($loop == $type) {
                return $loop;
            }
        }

        return null;
    }

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
     * Helper function for get meta score class
     *
     * @param $score
     * @return string
     */
    protected function getScoreClassByScore($score): string
    {
        if ($score >= 61) {
            return "positive";
        } elseif ($score >= 40) {
            return "mixed";
        } elseif ($score >= 0) {
            return "negative";
        }

        return "tbd";
    }

}
