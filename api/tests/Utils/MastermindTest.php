<?php

namespace App\Tests\Utils;

use App\Entity\Turn;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Utils\Mastermind;
use App\Entity\Game;
use Symfony\Component\Config\Definition\Exception\Exception;

class MastermindTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    protected $game_initialized;
    protected $game_won;
    protected $game_losed;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $games = $this->entityManager
            ->getRepository(Game::class)
            ->findAll()
        ;
        $this->game_initialized = new Mastermind($this->entityManager, $games[0]);
        $this->game_won = new Mastermind($this->entityManager, $games[1]);
        $this->game_losed = new Mastermind($this->entityManager, $games[2]);
    }

    public function testIsOver()
    {
        $this->assertEquals(false, $this->game_initialized->isOver());
        $this->assertEquals(true, $this->game_won->isOver());
        $this->assertEquals(true, $this->game_losed->isOver());
    }

    public function testPlayerWin()
    {
        $this->assertEquals(false, $this->game_initialized->playerWin());
        $this->assertEquals(true, $this->game_won->playerWin());
        $this->assertEquals(false, $this->game_losed->playerWin());
    }

    public function testCheckGuess()
    {
        $turn = new Turn();
        $guess = json_encode(array('RED','BLUE','YELLOW','RED'));
        $turn->setGuess($guess);
        $this->assertEquals(false, $this->game_initialized->checkGuess($turn));
        $guess = array('RED','BLUE','YELLOW');
        $turn->setGuess($guess);
        $this->assertEquals(false, $this->game_initialized->checkGuess($turn));
        $guess = array('RED','BLUE','YELLOW','NONE');
        $turn->setGuess($guess);
        $this->assertEquals(false, $this->game_initialized->checkGuess($turn));
        $guess = array('RED','BLUE','YELLOW','RED');
        $turn->setGuess($guess);
        $this->assertEquals(true, $this->game_initialized->checkGuess($turn));
    }

    public function testTurnNumber()
    {
        $this->assertEquals(0, $this->game_initialized->turnNumber());
        $this->assertEquals(1, $this->game_won->turnNumber());
        $this->assertEquals(2, $this->game_losed->turnNumber());
    }

    public function testGetHistoric()
    {
        $this->assertEquals(0, count($this->game_initialized->getHistoric()));
        $this->assertEquals(1, count($this->game_won->getHistoric()));
        $this->assertEquals(2, count($this->game_losed->getHistoric()));
    }

    public function testPlay()
    {
        $turn = new Turn();
        $numCodes = $this->game_initialized->getNumCodes();
        while(!$this->game_initialized->isOver()){
            $guess = array();
            foreach(range(0, $numCodes-1) as $num){
                $last = count($this->game_initialized->getListColors())-1;
                $guess[]=$this->game_initialized->getListColors()[rand(0,$last)];
            }
            $turn->setGuess($guess);
            $turn = $this->game_initialized->play($turn);
            $result = $turn->getResult();
            $this->assertEquals(true, 0<=$result['BLACK']+$result['WHITE']);
            $this->assertEquals(true, $numCodes>=$result['BLACK']+$result['WHITE']);
        }
        $this->assertEquals(true, $numCodes == $result['BLACK']);
    }

    public function testErrorPlayGameOver()
    {
        $turn = new Turn();
        $guess = array('RED','BLUE','YELLOW','RED');
        $turn->setGuess($guess);
        $this->expectException(Exception::class);
        $this->game_won->play($turn);
    }

    public function testErrorPlayBadGuess()
    {
        $turn = new Turn();
        $guess = array('RED','BLUE','YELLOW','NAN');
        $turn->setGuess($guess);
        $this->expectException(Exception::class);
        $this->game_initialized->play($turn);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}