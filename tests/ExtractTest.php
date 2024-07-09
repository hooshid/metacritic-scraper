<?php

use PHPUnit\Framework\TestCase;

class ExtractTest extends TestCase
{
    public function testExtractMovie()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->extract('/movie/the-matrix');
        $this->assertIsArray($result);
        $this->assertCount(17, $result['result']);

        $this->assertEquals('https://www.metacritic.com/movie/the-matrix', $result['result']['full_url']);
        $this->assertEquals('/movie/the-matrix', $result['result']['url']);
        $this->assertEquals('the-matrix', $result['result']['url_slug']);
        $this->assertEquals('The Matrix', $result['result']['title']);
        $this->assertEquals('https://www.metacritic.com/a/img/resize/7bd4e84a57871f38af5ac241aff388887162c5f2/catalog/provider/2/13/2-82ec049fbb9bc9bc3601207e06a81f5f.jpg?auto=webp&fit=crop&height=675&width=1200', $result['result']['thumbnail']);
        $this->assertEquals('1999', $result['result']['release_year']);
        $this->assertEquals('movie', $result['result']['type']);

        $this->assertGreaterThan(70, $result['result']['meta_score']);
        $this->assertLessThan(75, $result['result']['meta_score']);
        $this->assertGreaterThan(35, $result['result']['meta_votes']);

        $this->assertGreaterThan(8.5, $result['result']['user_score']);
        $this->assertLessThan(9.5, $result['result']['user_score']);
        $this->assertGreaterThan(1900, $result['result']['user_votes']);

        $this->assertEquals('A computer hacker (Keanu Reeves) learns that his entire life has been a virtual dream, orchestrated by a strange class of computer overlords in the far future. He joins a resistance movement to free humanity from lives of computerized brainwashing.', $result['result']['summary']);
        $this->assertEquals('Action, Sci-Fi', implode(', ', $result['result']['genres']));
        $this->assertFalse($result['result']['must_see']);
        $this->assertEquals('R', $result['result']['rating']);

        $this->assertIsArray($result['result']['cast']);
        $this->assertCount(20, $result['result']['cast']);

        $this->assertEquals('Keanu Reeves', $result['result']['cast'][0]['name']);
        $this->assertEquals('https://www.metacritic.com/person/keanu-reeves', $result['result']['cast'][0]['full_url']);
        $this->assertEquals('keanu-reeves', $result['result']['cast'][0]['url_slug']);

        $this->assertIsArray($result['result']['director']);
        $this->assertCount(2, $result['result']['director']);

        $this->assertNull($result['error']);
    }

    public function testExtractTV()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->extract('/tv/breaking-bad');
        $this->assertIsArray($result);
        $this->assertCount(17, $result['result']);

        $this->assertEquals('https://www.metacritic.com/tv/breaking-bad', $result['result']['full_url']);
        $this->assertEquals('/tv/breaking-bad', $result['result']['url']);
        $this->assertEquals('breaking-bad', $result['result']['url_slug']);
        $this->assertEquals('Breaking Bad', $result['result']['title']);
        $this->assertEquals('https://www.metacritic.com/a/img/resize/8bcd4b323f5ced1b68a40900645ea67c8a8f26a1/catalog/provider/2/13/2-94077e8f1aa339d2464cb30d6bec2d3f.jpg?auto=webp&fit=crop&height=675&width=1200', $result['result']['thumbnail']);
        $this->assertEquals('2008', $result['result']['release_year']);
        $this->assertEquals('tv', $result['result']['type']);

        $this->assertGreaterThan(85, $result['result']['meta_score']);
        $this->assertLessThan(90, $result['result']['meta_score']);
        $this->assertGreaterThan(95, $result['result']['meta_votes']);

        $this->assertGreaterThan(9, $result['result']['user_score']);
        $this->assertLessThan(9.5, $result['result']['user_score']);
        $this->assertGreaterThan(17700, $result['result']['user_votes']);

        $this->assertEquals('342', strlen($result['result']['summary']));
        $this->assertEquals('Crime, Drama, Thriller', implode(', ', $result['result']['genres']));
        $this->assertTrue($result['result']['must_see']);
        $this->assertEquals('TV-MA', $result['result']['rating']);

        $this->assertIsArray($result['result']['cast']);
        $this->assertCount(20, $result['result']['cast']);

        $this->assertEquals('Bryan Cranston', $result['result']['cast'][0]['name']);
        $this->assertEquals('https://www.metacritic.com/person/bryan-cranston', $result['result']['cast'][0]['full_url']);
        $this->assertEquals('bryan-cranston', $result['result']['cast'][0]['url_slug']);

        $this->assertIsArray($result['result']['director']);
        $this->assertCount(1, $result['result']['director']);

        $this->assertNull($result['error']);
    }

    public function testMovieNotFound()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->extract('/tv/not-found');
        $this->assertIsArray($result);
        $this->assertCount(3, $result['result']);

        $this->assertEquals(404, $result['error']);
    }
}
