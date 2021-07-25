<?php

use PHPUnit\Framework\TestCase;

class ExtractTest extends TestCase
{
    public function testExtractMovie()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->extract('/movie/the-matrix');
        $this->assertIsArray($result);
        $this->assertCount(18, $result['result']);

        $this->assertEquals('https://www.metacritic.com/movie/the-matrix', $result['result']['full_url']);
        $this->assertEquals('/movie/the-matrix', $result['result']['url']);
        $this->assertEquals('the-matrix', $result['result']['url_slug']);
        $this->assertEquals('The Matrix', $result['result']['title']);
        $this->assertEquals('https://static.metacritic.com/images/products/movies/5/14d38f138eb320954cd1e07d0449e5a6-250h.jpg', $result['result']['thumbnail']);
        $this->assertEquals('1999', $result['result']['release_year']);
        $this->assertEquals('movie', $result['result']['type']);

        $this->assertGreaterThan(70, $result['result']['meta_score']);
        $this->assertLessThan(75, $result['result']['meta_score']);
        $this->assertGreaterThan(34, $result['result']['meta_votes']);

        $this->assertGreaterThan(8.5, $result['result']['user_score']);
        $this->assertLessThan(9.5, $result['result']['user_score']);
        $this->assertGreaterThan(1700, $result['result']['user_votes']);

        $this->assertFalse($result['result']['must_see']);
        $this->assertEquals('A computer hacker (Reeves) learns that his entire life has been a virtual dream, orchestrated by a strange class of computer overlords in the far future. He joins a resistance movement (led by Fishburne) to free humanity from lives of computerized brainwashing.', $result['result']['summary']);
        $this->assertEquals('Carrie-Anne Moss, Keanu Reeves, Laurence Fishburne', implode(', ', $result['result']['starring']));
        $this->assertEquals('Andy Wachowski and Lana Wachowski', implode(' and ', $result['result']['director']));
        $this->assertEquals('Action, Adventure, Sci-Fi, Thriller', implode(', ', $result['result']['genres']));
        $this->assertEquals('R', $result['result']['rating']);
        $this->assertEquals('136 min', $result['result']['runtime']);

        $this->assertNull($result['error']);
    }

    public function testExtractTV()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->extract('/tv/breaking-bad');
        $this->assertIsArray($result);
        $this->assertCount(18, $result['result']);

        $this->assertEquals('https://www.metacritic.com/tv/breaking-bad', $result['result']['full_url']);
        $this->assertEquals('/tv/breaking-bad', $result['result']['url']);
        $this->assertEquals('breaking-bad', $result['result']['url_slug']);
        $this->assertEquals('Breaking Bad', $result['result']['title']);
        $this->assertEquals('https://static.metacritic.com/images/products/tv/2/ac04956caf6d3ec537238f8a6899a18b-98.jpg', $result['result']['thumbnail']);
        $this->assertEquals('2008', $result['result']['release_year']);
        $this->assertEquals('tv', $result['result']['type']);

        $this->assertGreaterThan(85, $result['result']['meta_score']);
        $this->assertLessThan(90, $result['result']['meta_score']);
        $this->assertGreaterThan(95, $result['result']['meta_votes']);

        $this->assertGreaterThan(9, $result['result']['user_score']);
        $this->assertLessThan(9.5, $result['result']['user_score']);
        $this->assertGreaterThan(825, $result['result']['user_votes']);

        $this->assertTrue($result['result']['must_see']);
        $this->assertEquals('340', strlen($result['result']['summary']));
        $this->assertEquals('Bryan Cranston, Bob Odenkirk, Anna Gunn, Jonathan Banks, Aaron Paul, Dean Norris, Betsy Brandt, RJ Mitte', implode(', ', $result['result']['starring']));
        $this->assertEquals('Vince Gilligan', implode(' and ', $result['result']['director']));
        $this->assertEquals('Drama, Action & Adventure, Suspense', implode(', ', $result['result']['genres']));
        $this->assertNull($result['result']['rating']);
        $this->assertNull($result['result']['runtime']);

        $this->assertNull($result['error']);
    }
}
