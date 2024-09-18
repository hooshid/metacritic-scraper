<?php

use PHPUnit\Framework\TestCase;

class ExtractTest extends TestCase
{
    public function testExtractMovie()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->extract('/movie/the-lord-of-the-rings-the-fellowship-of-the-ring');
        $this->assertIsArray($result);
        $this->assertCount(17, $result['result']);

        $this->assertEquals('https://www.metacritic.com/movie/the-lord-of-the-rings-the-fellowship-of-the-ring', $result['result']['full_url']);
        $this->assertEquals('/movie/the-lord-of-the-rings-the-fellowship-of-the-ring', $result['result']['url']);
        $this->assertEquals('the-lord-of-the-rings-the-fellowship-of-the-ring', $result['result']['url_slug']);
        $this->assertEquals('The Lord of the Rings: The Fellowship of the Ring', $result['result']['title']);
        $this->assertEquals('https://www.metacritic.com/a/img/resize/500cbedde2bb6b6c3d6f009550f32a044536d555/catalog/provider/2/13/2-5a34b4bbf494b7f675aba4f842086128.jpg?auto=webp&fit=crop&height=675&width=1200', $result['result']['thumbnail']);
        $this->assertEquals('2001', $result['result']['release_year']);
        $this->assertEquals('movie', $result['result']['type']);

        $this->assertGreaterThan(90, $result['result']['meta_score']);
        $this->assertLessThan(95, $result['result']['meta_score']);
        $this->assertGreaterThan(30, $result['result']['meta_votes']);

        $this->assertGreaterThan(8.5, $result['result']['user_score']);
        $this->assertLessThan(9.5, $result['result']['user_score']);
        $this->assertGreaterThan(2600, $result['result']['user_votes']);

        $this->assertEquals('239', strlen($result['result']['summary']));
        $this->assertEquals('Action, Adventure, Drama, Fantasy', implode(', ', $result['result']['genres']));
        $this->assertTrue($result['result']['must_see']);
        $this->assertEquals('PG-13', $result['result']['rating']);

        $this->assertIsArray($result['result']['cast']);
        $this->assertCount(20, $result['result']['cast']);

        $this->assertEquals('Elijah Wood', $result['result']['cast'][0]['name']);
        $this->assertEquals('https://www.metacritic.com/person/elijah-wood', $result['result']['cast'][0]['full_url']);
        $this->assertEquals('elijah-wood', $result['result']['cast'][0]['url_slug']);

        $this->assertIsArray($result['result']['director']);
        $this->assertCount(1, $result['result']['director']);

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
        $this->assertGreaterThan(17000, $result['result']['user_votes']);

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
        $result = $search->extract('/movie/not-found');
        $this->assertIsArray($result);
        $this->assertCount(3, $result['result']);

        $this->assertEquals(404, $result['error']);
    }
}
