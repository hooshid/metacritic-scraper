<?php

use Hooshid\MetacriticScraper\Metacritic;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testPerson()
    {
        $metacritic = new Metacritic();
        $result = $metacritic->person('tom-cruise');

        $this->assertIsArray($result);
        $this->assertCount(5, $result['result']);

        $this->assertEquals('https://www.metacritic.com/person/tom-cruise', $result['result']['full_url']);
        $this->assertEquals('tom-cruise', $result['result']['url_slug']);
        $this->assertEquals('Tom Cruise', $result['result']['name']);
        $this->assertIsArray($result['result']['movies']);
        $this->assertGreaterThanOrEqual(75, count($result['result']['movies']));
        $this->assertIsArray($result['result']['series']);
        $this->assertGreaterThanOrEqual(50, count($result['result']['series']));

        // find "Mission: Impossible – Dead Reckoning" movie
        $key = array_search("Mission: Impossible – Dead Reckoning", array_column($result['result']['movies'], 'title'));
        $this->assertTrue($key !== false);
        $this->assertEquals('Mission: Impossible – Dead Reckoning', $result['result']['movies'][$key]['title']);
        $this->assertEquals('https://www.metacritic.com/movie/mission-impossible-dead-reckoning', $result['result']['movies'][$key]['url']);
        $this->assertEquals('mission-impossible-dead-reckoning', $result['result']['movies'][$key]['url_slug']);
        $this->assertEquals(2023, $result['result']['movies'][$key]['year']);

        // find "Top Gun" movie
        $key = array_search("Top Gun", array_column($result['result']['movies'], 'title'));
        $this->assertTrue($key !== false);
        $this->assertEquals('Top Gun', $result['result']['movies'][$key]['title']);
        $this->assertEquals('https://www.metacritic.com/movie/top-gun', $result['result']['movies'][$key]['url']);
        $this->assertEquals('top-gun', $result['result']['movies'][$key]['url_slug']);
        $this->assertEquals(1986, $result['result']['movies'][$key]['year']);

        $this->assertNull($result['error']);
    }

    public function testPersonNotFound()
    {
        $metacritic = new Metacritic();
        $result = $metacritic->person('not-found');

        $this->assertIsArray($result);
        $this->assertCount(5, $result['result']);

        $this->assertEquals('https://www.metacritic.com/person/not-found', $result['result']['full_url']);
        $this->assertEquals('not-found', $result['result']['url_slug']);
        $this->assertNull($result['result']['name']);
        $this->assertIsArray($result['result']['movies']);
        $this->assertEmpty($result['result']['movies']);
        $this->assertIsArray($result['result']['series']);
        $this->assertEmpty($result['result']['series']);

        $this->assertEquals(404, $result['error']);
    }
}
