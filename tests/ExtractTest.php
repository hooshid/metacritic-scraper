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
        $this->assertGreaterThan(34, $result['result']['meta_votes']);

        $this->assertGreaterThan(8.5, $result['result']['user_score']);
        $this->assertLessThan(9.5, $result['result']['user_score']);
        $this->assertGreaterThan(1700, $result['result']['user_votes']);

        $this->assertFalse($result['result']['must_see']);
        $this->assertEquals('A computer hacker (Keanu Reeves) learns that his entire life has been a virtual dream, orchestrated by a strange class of computer overlords in the far future. He joins a resistance movement to free humanity from lives of computerized brainwashing.', $result['result']['summary']);
        $this->assertEquals('Action, Sci-Fi', implode(', ', $result['result']['genres']));
        $this->assertEquals('R', $result['result']['rating']);
        $this->assertEquals('136 min', $result['result']['runtime']);

        $this->assertIsArray($result['result']['cast']);
        $this->assertCount(20, $result['result']['cast']);

        $this->assertEquals('Keanu Reeves', $result['result']['cast'][0]['name']);
        $this->assertEquals('https://www.metacritic.com/person/keanu-reeves', $result['result']['cast'][0]['full_url']);
        $this->assertEquals('keanu-reeves', $result['result']['cast'][0]['url_slug']);

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
        $this->assertGreaterThan(825, $result['result']['user_votes']);

        $this->assertTrue($result['result']['must_see']);
        $this->assertEquals('342', strlen($result['result']['summary']));

        $this->assertEquals('Crime, Drama, Thriller', implode(', ', $result['result']['genres']));
        $this->assertNull($result['result']['rating']);
        $this->assertNull($result['result']['runtime']);

        $this->assertIsArray($result['result']['cast']);
        $this->assertCount(20, $result['result']['cast']);
        $this->assertEquals('Bryan Cranston', $result['result']['cast'][0]['name']);
        $this->assertEquals('https://www.metacritic.com/person/bryan-cranston', $result['result']['cast'][0]['full_url']);
        $this->assertEquals('bryan-cranston', $result['result']['cast'][0]['url_slug']);

        $this->assertNull($result['error']);
    }

    public function testExtractMusic()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->extract('/music/happier-than-ever/billie-eilish');
        $this->assertIsArray($result);
        $this->assertCount(14, $result['result']);

        $this->assertEquals('https://www.metacritic.com/music/happier-than-ever/billie-eilish', $result['result']['full_url']);
        $this->assertEquals('/music/happier-than-ever/billie-eilish', $result['result']['url']);
        $this->assertEquals('billie-eilish', $result['result']['url_slug']);
        $this->assertEquals('Happier than Ever', $result['result']['title']);
        $this->assertEquals('https://static.metacritic.com/images/products/music/4/35acaa79ec2e95dd3efa0e581a845970-98.jpg', $result['result']['thumbnail']);
        $this->assertEquals('2021', $result['result']['release_year']);
        $this->assertEquals('music', $result['result']['type']);

        $this->assertGreaterThan(85, $result['result']['meta_score']);
        $this->assertLessThan(90, $result['result']['meta_score']);
        $this->assertGreaterThan(25, $result['result']['meta_votes']);

        $this->assertGreaterThan(8, $result['result']['user_score']);
        $this->assertLessThan(9, $result['result']['user_score']);
        $this->assertGreaterThan(820, $result['result']['user_votes']);

        $this->assertEquals('The second full-length studio release for the Los Angeles pop singer-songwriter was produced by her brother, Finneas.', $result['result']['summary']);
        $this->assertEquals('117', strlen($result['result']['summary']));
        $this->assertEquals('Pop/Rock', implode(', ', $result['result']['genres']));
        $this->assertEquals('Billie Eilish', $result['result']['artist']);

        $this->assertNull($result['error']);
    }

    public function testExtractGame()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->extract('/game/xbox-series-x/microsoft-flight-simulator');
        $this->assertIsArray($result);
        $this->assertCount(19, $result['result']);

        $this->assertEquals('https://www.metacritic.com/game/xbox-series-x/microsoft-flight-simulator', $result['result']['full_url']);
        $this->assertEquals('/game/xbox-series-x/microsoft-flight-simulator', $result['result']['url']);
        $this->assertEquals('microsoft-flight-simulator', $result['result']['url_slug']);
        $this->assertEquals('Microsoft Flight Simulator', $result['result']['title']);
        $this->assertEquals('https://static.metacritic.com/images/products/games/7/1cc2e1878b4f5e9252f9f3b740e99125-98.jpg', $result['result']['thumbnail']);
        $this->assertEquals('2021', $result['result']['release_year']);
        $this->assertEquals('game', $result['result']['type']);

        $this->assertGreaterThan(85, $result['result']['meta_score']);
        $this->assertLessThan(95, $result['result']['meta_score']);
        $this->assertGreaterThan(29, $result['result']['meta_votes']);

        $this->assertGreaterThan(7, $result['result']['user_score']);
        $this->assertLessThan(9, $result['result']['user_score']);
        $this->assertGreaterThan(277, $result['result']['user_votes']);

        $this->assertEquals('From light planes to wide-body jets, fly highly detailed and accurate aircraft in the next generation of Microsoft Flight Simulator. Test your piloting skills against the challenges of night flying, real-time atmospheric simulation and live weather in a dynamic and living world.', $result['result']['summary']);
        $this->assertEquals('279', strlen($result['result']['summary']));
        $this->assertEquals('Simulation, Flight, Civilian', implode(', ', $result['result']['genres']));
        $this->assertEquals('Asobo Studio', implode(', ', $result['result']['developers']));
        $this->assertEquals('Microsoft Game Studios, Microsoft, Xbox Game Studios', implode(', ', $result['result']['publishers']));
        $this->assertIsArray($result['result']['also_on']);
        $this->assertCount(2, $result['result']['also_on']);
        $this->assertEquals('https://www.gamefaqs.com/console/xbox-series-x/code/265963.html', $result['result']['cheat_url']);
        $this->assertEquals('Xbox Series X', $result['result']['platform']);

        $this->assertNull($result['error']);
    }
}
