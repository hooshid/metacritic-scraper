<?php

namespace Hooshid\MetacriticScraper\Base;

class Base
{
    protected string $baseUrl = 'https://www.metacritic.com';

    protected array $searchTypes = ['all', 'movie', 'tv', 'person'];

    /**
     * Get html content
     *
     * @param string $url
     * @return bool|string
     */
    protected function getContentPage(string $url): bool|string
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
     * @param string|null $str
     * @param null $remove
     * @return string|null
     */
    protected function cleanString(string|null $str, $remove = null): ?string
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
     * @param string $str
     * @param string $needle
     * @return string
     */
    protected function afterLast(string $str, string $needle = '/'): string
    {
        return substr($str, strrpos($str, $needle) + 1);
    }

    /**
     * extract numbers from string
     *
     * @param string $str
     * @return int
     */
    protected function getNumbers(string $str): int
    {
        return (int)filter_var($str, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * @return mixed|null
     */
    protected function jsonLD($html): mixed
    {
        if (empty($html)) return [];
        preg_match('#<script data-n-head="ssr" charset="UTF-8" type="application/ld\+json" data-hid="ld\+json">(.+?)</script>#ims', $html, $matches);
        if (empty($matches[1])) return [];
        return json_decode($matches[1]);
    }

    /**
     * get type of page or result
     *
     * @param string $type
     * @return string|null
     */
    protected function getType(string $type): ?string
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
     * @param int|null $score
     * @return string
     */
    protected function getScoreClassByScore(?int $score): string
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
