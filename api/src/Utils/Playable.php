<?php

namespace App\Utils;
use App\Entity\Turn;

interface Playable
{
    /**
     * Function to check the status of the game.
     *
     * @return boolean True for game over
     */
    public function isOver();

    /**
     * Function to check the game result.
     *
     * @return boolean True for game won by player
     */
    public function playerWin();

    /**
     * Function to check a guess, usefull to validate the information sent by gamer
     *
     * @return boolean True for valid guesses
     */
    public function checkGuess(Turn $turn);


    /**
     * Function to execute a guess from a player
     *
     * @return Turn object with all the information of the turn result
     */
    public function play(Turn $turn);

    /**
     * Function to know how many turns have the game
     *
     * @return int
     */
    public function turnNumber();

    /**
     * Function to read all the game's turns
     *
     * @return string
     */
    public function getHistoric();
}
