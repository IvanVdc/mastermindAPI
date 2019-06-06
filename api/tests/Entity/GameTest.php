<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Game;
use Symfony\Component\Config\Definition\Exception\Exception;

class GameTest extends TestCase
{

    public function testSettersAndGetters()
    {
        $game = new Game();
        //Default values
        $this->assertEquals(false, $game->getOver());
        $this->assertEquals(false, $game->getPlayerWin());
        $this->assertEquals(12, $game->getMaxTries());
        $this->assertEquals(4, $game->getNumCodes());
        $this->assertEquals(null, $game->getUser());
        $this->assertEquals(4, count($game->getGoal()));
        foreach (range(0, $game->getNumCodes()-1) as $numero) {
            $this->assertTrue(in_array($game->getGoal()[$numero], $game->getListColors()));
        }

        //Update default data
        $game = new Game(15,5);
        $game->setOver(true);
        $game->setPlayerWin(true);
        $this->assertEquals(true, $game->getOver());
        $this->assertEquals(true, $game->getPlayerWin());
        $this->assertEquals(15, $game->getMaxTries());
        $this->assertEquals(5, $game->getNumCodes());
    }

    public function testCombination(){
        $game = new Game();
        $combination = json_encode(array('RED','BLUE','YELLOW','RED'));
        $this->assertEquals(false, $game->checkCombination($combination));
        $combination = array('RED','BLUE','YELLOW');
        $this->assertEquals(false, $game->checkCombination($combination));
        $combination = array('RED','BLUE','YELLOW','NAN');
        $this->assertEquals(false, $game->checkCombination($combination));
        $combination = array('RED','BLUE','YELLOW','RED');
        $this->assertEquals(true, $game->checkCombination($combination));
    }

    public function testGetResult(){
        $game = new Game();
        $guess = $game->getGoal();
        $result = $game->getResult($guess);
        $this->assertEquals(4, $result['BLACK']);
        $this->assertEquals(0, $result['WHITE']);

        $guess[0]='NAN';
        $result = $game->getResult($guess);
        $this->assertEquals(3, $result['BLACK']);
        $this->assertEquals(0, $result['WHITE']);

        $guess[1]='NAN';
        $result = $game->getResult($guess);
        $this->assertEquals(2, $result['BLACK']);
        $this->assertEquals(0, $result['WHITE']);

        $guess[2]='NAN';
        $result = $game->getResult($guess);
        $this->assertEquals(1, $result['BLACK']);
        $this->assertEquals(0, $result['WHITE']);

        $guess[0]=$game->getGoal()[1];
        $result = $game->getResult($guess);
        $this->assertEquals(2, $result['BLACK']+$result['WHITE']);

        $guess[2]=$game->getGoal()[0];
        $result = $game->getResult($guess);
        $this->assertEquals(3, $result['BLACK']+$result['WHITE']);
    }
}
