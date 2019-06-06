<?php

namespace App\Tests\Utils;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Utils\Player;
use App\Entity\User;
use Symfony\Component\Config\Definition\Exception\Exception;

class PlayerTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    protected $player_without_games;
    protected $player_playing;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $players = $this->entityManager
            ->getRepository(User::class)
            ->findAll()
        ;

        $this->player_without_games = new Player($this->entityManager, $players[0]);
        $this->player_playing = new Player($this->entityManager, $players[1]);
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

    public function testCanCreateGame()
    {
        $this->assertEquals(true, $this->player_without_games->canCreateNewGame());
        $game = $this->player_without_games->CreateNewGame();
        $this->assertEquals(true, ($game instanceof Game));
    }

    public function testCanCreateCustomGame()
    {
        $this->assertEquals(true, $this->player_without_games->canCreateNewGame());
        $game = $this->player_without_games->CreateCustomNewGame(15,5);
        $this->assertEquals(true, ($game instanceof Game));
    }

    public function testGetGame()
    {
        $game = $this->player_playing->getGame();
        $this->assertEquals(true, ($game instanceof Game));
    }

    public function testErrorGetGame()
    {
        $this->expectException(Exception::class);
        $this->player_without_games->getGame();
    }

    public function testErrorCanCreateGame()
    {
        $this->expectException(Exception::class);
        $this->assertEquals(false, $this->player_playing->canCreateNewGame());
        $game = $this->player_playing->CreateNewGame();
        $this->assertEquals(false, ($game instanceof Game));
    }

    public function testGetHistoric()
    {
        $this->assertEquals(0, count($this->player_without_games->getHistoric()));
        $this->assertEquals(3, count($this->player_playing->getHistoric()));
    }
}