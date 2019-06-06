<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Turn;

class TurnTest extends TestCase
{
    protected $turn;

    public function setUp()
    {
        $this->turn = new Turn();
    }

    public function testSettersAndGetters()
    {
        $guess = json_encode(array('RED','BLUE','YELLOW','RED'));
        $result = json_encode(array('BLACK'=>'1','WHITE'=>'1'));
        $this->turn->setGuess($guess);
        $this->turn->setResult($result);
        $this->assertEquals(null, $this->turn->getGame());
        $this->assertEquals($guess, $this->turn->getGuess());
        $this->assertEquals($result, $this->turn->getResult());
    }
}
