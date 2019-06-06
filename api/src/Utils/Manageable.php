<?php

namespace App\Utils;

interface Manageable
{
    /**
     * Function to check if user has a opened game.
     *
     * @return boolean True for success
     */
    public function canCreateNewGame();

    /**
     * Function to create a new game.
     *
     * @return Game
     */
    public function createNewGame();
    public function createCustomNewGame($maxTries=null, $numCodes=null);

    /**
     * Function to read all the game played
     *
     * @return string
     */
    public function getHistoric();
}
