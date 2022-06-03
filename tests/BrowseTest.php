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


        $this->assertEquals('https://www.metacritic.com/movie/the-godfather', $result['results'][0]['full_url']);
        $this->assertEquals('/movie/the-godfather', $result['results'][0]['url']);
        $this->assertEquals('the-godfather', $result['results'][0]['url_slug']);
        $this->assertEquals('The Godfather', $result['results'][0]['title']);
        $this->assertEquals('Francis Ford Coppola\'s epic features Marlon Brando in his Oscar-winning role as the patriarch of the Corleone family. Director Coppola paints a chilling portrait of the Sicilian clan\'s rise and near fall from power in America, masterfully balancing the story between the Corleone\'s family life and the ugly crime business in which they are engaged. Based on Mario Puzo\'s best-selling novel and featuring career-making performances by Al Pacino, James Caan and Robert Duvall, this searing and brilliant film garnered ten Academy Award nominations, and won three including Best Picture of 1972. [Paramount Pictures]', $result['results'][0]['description']);
        $this->assertEquals('https://static.metacritic.com/images/products/movies/3/47c2b1f35087fc23c5ce261bbc3ad9e0-98.jpg', $result['results'][0]['thumbnail']);
        $this->assertEquals('1972', $result['results'][0]['year']);
        $this->assertEquals('movie', $result['results'][0]['type']);
        $this->assertEquals('1', $result['results'][0]['number']);
        $this->assertGreaterThan(99, $result['results'][0]['meta_score']);
        $this->assertTrue($result['results'][0]['must_see']);
        $this->assertGreaterThan(8.0, $result['results'][0]['user_score']);
        $this->assertLessThan(9.5, $result['results'][0]['user_score']);
        $this->assertEquals('positive', $result['results'][0]['score_class']);
        $this->assertEquals('positive', $result['results'][0]['user_score_class']);


        $this->assertEquals('https://www.metacritic.com/movie/citizen-kane', $result['results'][1]['full_url']);
        $this->assertEquals('/movie/citizen-kane', $result['results'][1]['url']);
        $this->assertEquals('citizen-kane', $result['results'][1]['url_slug']);
        $this->assertEquals('Citizen Kane', $result['results'][1]['title']);
        $this->assertEquals('Following the death of a publishing tycoon, news reporters scramble to discover the meaning of his final utterance.', $result['results'][1]['description']);
        $this->assertEquals('https://static.metacritic.com/images/products/movies/5/1c4da52a6f2335836a21271ec4a6f6b3-98.jpg', $result['results'][1]['thumbnail']);
        $this->assertEquals('1941', $result['results'][1]['year']);
        $this->assertEquals('movie', $result['results'][1]['type']);
        $this->assertEquals('2', $result['results'][1]['number']);
        $this->assertGreaterThan(99, $result['results'][1]['meta_score']);
        $this->assertTrue($result['results'][1]['must_see']);
        $this->assertGreaterThan(8.0, $result['results'][1]['user_score']);
        $this->assertLessThan(8.5, $result['results'][1]['user_score']);
        $this->assertEquals('positive', $result['results'][1]['score_class']);
        $this->assertEquals('positive', $result['results'][1]['user_score_class']);
    }
}
