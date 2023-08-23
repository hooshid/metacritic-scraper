<?php

use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testPerson()
    {
        $search = new \Hooshid\MetacriticScraper\Metacritic();
        $result = $search->person('tom-cruise');
        $this->assertIsArray($result);
        $this->assertCount(6, $result['result']);

        $this->assertEquals('https://www.metacritic.com/person/tom-cruise', $result['result']['full_url']);
        $this->assertEquals('tom-cruise', $result['result']['url_slug']);
        $this->assertEquals('Tom Cruise', $result['result']['name']);
        $this->assertGreaterThan(2500, strlen($result['result']['bio']));
        $this->assertIsArray($result['result']['movies']);
        $this->assertIsArray($result['result']['tv']);

        $this->assertNull($result['error']);
    }
}
