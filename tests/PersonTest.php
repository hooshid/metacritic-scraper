<?php

use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testPerson()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->person('tom-cruise');
        $this->assertIsArray($result);
        $this->assertCount(5, $result['result']);

        $this->assertEquals('https://www.metacritic.com/person/tom-cruise', $result['result']['full_url']);
        $this->assertEquals('tom-cruise', $result['result']['url_slug']);
        $this->assertEquals('Tom Cruise', $result['result']['name']);
        $this->assertIsArray($result['result']['movies']);
        $this->assertGreaterThan(70, count($result['result']['movies']));
        $this->assertIsArray($result['result']['series']);
        $this->assertGreaterThan(35, count($result['result']['series']));

        // find "The Last Samurai" movie
        $key = array_search("The Last Samurai", array_column($result['result']['movies'], 'title'));
        $this->assertTrue($key !== false);
        $this->assertEquals('The Last Samurai', $result['result']['movies'][$key]['title']);
        $this->assertEquals('https://www.metacritic.com/movie/the-last-samurai', $result['result']['movies'][$key]['url']);
        $this->assertEquals('the-last-samurai', $result['result']['movies'][$key]['url_slug']);
        $this->assertEquals(2003, $result['result']['movies'][$key]['year']);

        $this->assertNull($result['error']);
    }

    public function testPersonNotFound()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->person('not-found');
        $this->assertIsArray($result);
        $this->assertCount(5, $result['result']);
        $this->assertNull( $result['result']['name']);
        $this->assertEquals(404, $result['error']);
    }
}
