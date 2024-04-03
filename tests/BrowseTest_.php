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

        $index = 1;
        $this->assertEquals('https://www.metacritic.com/movie/the-godfather', $result['results'][$index]['full_url']);
        $this->assertEquals('/movie/the-godfather', $result['results'][$index]['url']);
        $this->assertEquals('the-godfather', $result['results'][$index]['url_slug']);
        $this->assertEquals('The Godfather', $result['results'][$index]['title']);
        $this->assertEquals('Francis Ford Coppola\'s epic features Marlon Brando in his Oscar-winning role as the patriarch of the Corleone family. Director Coppola paints a chilling portrait of the Sicilian clan\'s rise and near fall from power in America, masterfully balancing the story between the Corleone\'s family life and the ugly crime business in which they are engaged. Based on Mario Puzo\'s best-selling novel and featuring career-making performances by Al Pacino, James Caan and Robert Duvall, this searing and brilliant film garnered ten Academy Award nominations, and won three including Best Picture of 1972. [Paramount Pictures]', $result['results'][$index]['description']);
        $this->assertEquals('https://static.metacritic.com/images/products/movies/3/47c2b1f35087fc23c5ce261bbc3ad9e0-98.jpg', $result['results'][$index]['thumbnail']);
        $this->assertEquals('1972', $result['results'][$index]['year']);
        $this->assertEquals('movie', $result['results'][$index]['type']);
        $this->assertEquals($index+1, $result['results'][$index]['number']);
        $this->assertGreaterThan(99, $result['results'][$index]['meta_score']);
        $this->assertTrue($result['results'][$index]['must_see']);
        $this->assertGreaterThan(8.0, $result['results'][$index]['user_score']);
        $this->assertLessThan(9.5, $result['results'][$index]['user_score']);
        $this->assertEquals('positive', $result['results'][$index]['score_class']);
        $this->assertEquals('positive', $result['results'][$index]['user_score_class']);

        $index = 2;
        $this->assertEquals('https://www.metacritic.com/movie/citizen-kane', $result['results'][$index]['full_url']);
        $this->assertEquals('/movie/citizen-kane', $result['results'][$index]['url']);
        $this->assertEquals('citizen-kane', $result['results'][$index]['url_slug']);
        $this->assertEquals('Citizen Kane', $result['results'][$index]['title']);
        $this->assertEquals('Following the death of a publishing tycoon, news reporters scramble to discover the meaning of his final utterance.', $result['results'][$index]['description']);
        $this->assertEquals('https://static.metacritic.com/images/products/movies/5/1c4da52a6f2335836a21271ec4a6f6b3-98.jpg', $result['results'][$index]['thumbnail']);
        $this->assertEquals('1941', $result['results'][$index]['year']);
        $this->assertEquals('movie', $result['results'][$index]['type']);
        $this->assertEquals($index+1, $result['results'][$index]['number']);
        $this->assertGreaterThan(99, $result['results'][$index]['meta_score']);
        $this->assertTrue($result['results'][$index]['must_see']);
        $this->assertGreaterThan(8.0, $result['results'][$index]['user_score']);
        $this->assertLessThan(8.5, $result['results'][$index]['user_score']);
        $this->assertEquals('positive', $result['results'][$index]['score_class']);
        $this->assertEquals('positive', $result['results'][$index]['user_score_class']);
    }
}
