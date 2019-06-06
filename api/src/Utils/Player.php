<?php

namespace App\Utils;

use App\Entity\User;
use App\Entity\Game;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

class Player implements Manageable
{
    private $player;
    private $entityManager;
    private $env;

    public function __construct(EntityManager $entityManager, User $player){
        $this->entityManager = $entityManager;
        $this->player = $player;
        $this->env = $_SERVER['APP_ENV'];
    }

    //A new game can be created if no one other game has been initiated by user and not over
    public function canCreateNewGame(){
        $games = $this->entityManager->getRepository("App:Game")->findBy(array(
                "user" => $this->player->getId(),
                "over" => false
            )
        );
        if(count($games)>0){
            return false;
        }
        return true;
    }

    public function createNewGame(){
        if(!$this->canCreateNewGame()){
            throw new Exception('Player is not able to create a new game.');
        }
        $game = new Game();
        $game->setUser($this->player);
        $this->entityManager->persist($game);
        if($this->env!=='test') {
            $this->entityManager->flush();
        }
        return $game;
    }

    public function createCustomNewGame($maxTries=null, $numCodes=null){
        if(!$this->canCreateNewGame()){
            throw new Exception('Player is not able to create a new game.');
        }
        $game = new Game($maxTries, $numCodes);
        $game->setUser($this->player);
        $this->entityManager->persist($game);
        if($this->env!=='test') {
            $this->entityManager->flush();
        }
        return $game;
    }

    public function getGame(){
        $games = $this->entityManager->getRepository("App:Game")->findBy(array(
                "user" => $this->player->getId()
            ),
            array('id' => 'ASC')
        );
        if(!$games){
            throw new Exception('Player has not any game.');
        }
        return $games[count($games)-1];
    }

    public function getHistoric(){
        $games = $this->entityManager->getRepository("App:Game")->findBy(array(
                "user" => $this->player->getId()
            ),
            array('id' => 'ASC')
        );
        $result = array();
        foreach($games as $game){
            $gameDate = array();
            $gameDate['id'] = $game->getId();
            if($game->getOver()) {
                $gameDate['over'] = true;
                $gameDate['result'] = 'Winner';
            }
            else{
                $gameDate['over'] = false;
            }
            $result[]=$gameDate;
        }
        return $result;
    }
}