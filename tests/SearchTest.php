<?php

use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    public function testSearchMovie()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->search('Black Panther');
        $this->assertIsArray($result);
        $this->assertCount(100, $result['results']);

        $this->assertEquals('https://www.metacritic.com/movie/black-panther', $result['results'][0]['full_url']);
        $this->assertEquals('/movie/black-panther', $result['results'][0]['url']);
        $this->assertEquals('black-panther', $result['results'][0]['url_slug']);
        $this->assertEquals('Black Panther', $result['results'][0]['title']);
        $this->assertEquals('After the events of Captain America: Civil War, King T’Challa returns home to the reclusive, technologically advanced African nation of Wakanda to serve as his country’s new leader. However, T’Challa soon finds that he is challenged for the throne from factions within his own country. When two foes conspire to destroy Wakanda, the hero known as Black Panther must team up with C.I.A. agent Everett K. Ross and members of the Dora Milaje, Wakanadan special forces, to prevent Wakanda from being dragged into a world war.', $result['results'][0]['description']);
        $this->assertNull($result['results'][0]['thumbnail']);
        $this->assertEquals('2018', $result['results'][0]['year']);
        $this->assertEquals('movie', $result['results'][0]['type']);
        $this->assertGreaterThan(85, $result['results'][0]['meta_score']);
        $this->assertLessThan(90, $result['results'][0]['meta_score']);
        $this->assertTrue($result['results'][0]['must_see']);
        $this->assertEquals('positive', $result['results'][0]['score_class']);
    }

    public function testSearchTV()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->search('Game of Thrones');
        $this->assertIsArray($result);
        $this->assertCount(100, $result['results']);

        $this->assertEquals('https://www.metacritic.com/tv/game-of-thrones', $result['results'][0]['full_url']);
        $this->assertEquals('/tv/game-of-thrones', $result['results'][0]['url']);
        $this->assertEquals('game-of-thrones', $result['results'][0]['url_slug']);
        $this->assertEquals('Game of Thrones', $result['results'][0]['title']);
        $this->assertEquals('Adapted from George R.R. Martin\'s epic fantasy novel series "A Song of Ice and Fire", this series is about a fantasy world where royal houses battle for the Iron Throne.', $result['results'][0]['description']);
        $this->assertNull($result['results'][0]['thumbnail']);
        $this->assertEquals('2011', $result['results'][0]['year']);
        $this->assertEquals('tv', $result['results'][0]['type']);
        $this->assertGreaterThan(80, $result['results'][0]['meta_score']);
        $this->assertLessThan(90, $result['results'][0]['meta_score']);
        $this->assertTrue($result['results'][0]['must_see']);
        $this->assertEquals('positive', $result['results'][0]['score_class']);
    }
}
