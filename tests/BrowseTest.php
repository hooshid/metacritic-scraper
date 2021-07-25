<?php

use PHPUnit\Framework\TestCase;

class BrowseTest extends TestCase
{
    public function testBrowseMovies()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->browse('/browse/movies/score/metascore/all/filtered?sort=desc');
        $this->assertIsArray($result);
        $this->assertCount(100, $result['results']);

        $this->assertEquals('https://www.metacritic.com/movie/citizen-kane', $result['results'][0]['full_url']);
        $this->assertEquals('/movie/citizen-kane', $result['results'][0]['url']);
        $this->assertEquals('citizen-kane', $result['results'][0]['url_slug']);
        $this->assertEquals('Citizen Kane', $result['results'][0]['title']);
        $this->assertEquals('Following the death of a publishing tycoon, news reporters scramble to discover the meaning of his final utterance.', $result['results'][0]['description']);
        $this->assertEquals('https://static.metacritic.com/images/products/movies/5/1c4da52a6f2335836a21271ec4a6f6b3-98.jpg', $result['results'][0]['thumbnail']);
        $this->assertEquals('1941', $result['results'][0]['year']);
        $this->assertEquals('movie', $result['results'][0]['type']);
        $this->assertEquals('1', $result['results'][0]['number']);
        $this->assertGreaterThan(99, $result['results'][0]['meta_score']);
        $this->assertTrue($result['results'][0]['must_see']);
        $this->assertGreaterThan(8.0, $result['results'][0]['user_score']);
        $this->assertLessThan(8.5, $result['results'][0]['user_score']);
        $this->assertEquals('positive', $result['results'][0]['score_class']);
        $this->assertEquals('positive', $result['results'][0]['user_score_class']);
    }
}
