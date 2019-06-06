<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Game;
use Symfony\Component\Config\Definition\Exception\Exception;

class GameTest extends TestCase
{
    protected $game;

    public function setUp()
    {
        $this->game = new Game();
    }

    public function testSettersAndGetters()
    {
        //Default values
        $this->assertEquals(false, $this->game->getOver());
        $this->assertEquals(false, $this->game->getPlayerWin());
        $this->assertEquals(12, $this->game->getMaxTries());
        $this->assertEquals(4, $this->game->getNumCodes());

        //Update data
        $goal = array('RED','BLUE','YELLOW','RED');
        $this->game->setGoal($goal);
        $this->assertEquals(null, $this->game->getUser());
        $this->assertEquals($goal, $this->game->getGoal());

        //Update default data
        $this->game->setOver(true);
        $this->game->setPlayerWin(true);
        $this->game->setMaxTries(15);
        $this->game->setNumCodes(5);
        $this->assertEquals(true, $this->game->getOver());
        $this->assertEquals(true, $this->game->getPlayerWin());
        $this->assertEquals(15, $this->game->getMaxTries());
        $this->assertEquals(5, $this->game->getNumCodes());

    }

    public function testErrorSetGoalNotArray(){
        $this->expectException(Exception::class);
        $this->game->setNumCodes(4);
        $goal = json_encode(array('RED','BLUE','YELLOW','RED'));
        $this->game->setGoal($goal);
    }

    public function testErrorSetGoalBadNum(){
        $this->expectException(Exception::class);
        $this->game->setNumCodes(4);
        $goal = array('RED','BLUE','YELLOW');
        $this->game->setGoal($goal);
    }

    public function testErrorSetGoalBadColor(){
        $this->expectException(Exception::class);
        $this->game->setNumCodes(4);
        $goal = array('RED','BLUE','YELLOW','NONE');
        $this->game->setGoal($goal);
    }
}
