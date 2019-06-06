<?php

namespace App\Utils;

use App\Entity\Turn;
use App\Entity\Game;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

class Mastermind implements Playable
{
    private $game;
    private $entityManager;
    private $env;

    public function __construct(EntityManager $entityManager, Game $game){
        $this->entityManager = $entityManager;
        $this->game = $game;
        $this->env = $_SERVER['APP_ENV'];
    }

    //Check if a game is over, because the user won or because the maximum number of movements has been done
    public function isOver(){
        return $this->game->getOver();
    }

    public function playerWin(){
        return $this->game->getPlayerWin();
    }

    public function getNumCodes(){
        return $this->game->getNumCodes();
    }

    public function getListColors(){
        return $this->game->getListColors();
    }

    public function checkGuess(Turn $turn){
        return $this->game->checkCombination($turn->getGuess());
    }

    public function turnNumber(){
        $turns = $this->entityManager->getRepository("App:Turn")->findBy(array(
                "game" => $this->game->getId()
            )
        );
        return count($turns);
    }

    public function getHistoric(){
        $turns = $this->entityManager->getRepository("App:Turn")->findBy(array(
                "game" => $this->game->getId()
            )
        );

        $list = array();
        $i = 0;
        foreach($turns as $turn){
            $i++;
            $turnData = array();
            $turnData['id'] = $i;
            $turnData['num'] = $i;
            $turnData['guess'] = $turn->getGuess();
            $turnData['result'] = $turn->getResult();
            $list[]=$turnData;
        }
        return $list;
    }

    public function play(Turn $turn){
        if($this->isOver()){
            throw new Exception('The game is over.');
        }
        if(!$this->checkGuess($turn)){
            throw new Exception('The guess is not valid.');
        }
        $result = $this->game->getResult($turn->getGuess());
        $turn->setResult($result);
        $this->entityManager->persist($turn);
        if($this->env!=='test') {
            $this->entityManager->flush();
        }

        if($result['BLACK']==$this->game->getNumCodes()){
            $this->game->setOver(true);
            $this->game->setPlayerWin(true);
            $this->entityManager->persist($this->game);
        }
        elseif($this->game->getMaxTries() == $this->turnNumber()){
            $this->game->setOver(true);
            $this->entityManager->persist($this->game);
        }

        if($this->env!=='test') {
            $this->entityManager->flush();
        }
        return $turn;
    }
}