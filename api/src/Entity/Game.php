<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity to save the game info
 *
 * @ApiResource
 * @ORM\Entity
 */
class Game
{
    private  $listColors = array('GREEN', 'RED', 'BLUE', 'YELLOW', 'PINK', 'ORANGE', 'BROWN', 'GREY');

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="games")
     */
    private $user;

    /**
     * @Assert\NotBlank
     * @Assert\GreaterThan(
     *     value = 0
     * )
     * @ORM\Column(type="integer")
     */
    private $maxTries;

    /**
     * @Assert\NotBlank
     * @Assert\GreaterThan(
     *     value = 0
     * )
     * @ORM\Column(type="integer")
     */
    private $numCodes;

    /**
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @ORM\Column(type="string")
     */
    private $goal;

    /**
     * @ORM\Column(type="boolean")
     */
    private $over = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $playerWin = false;

    /**
     * Constructor function.
     */
    public function __construct()
    {
        $this->over = false;
        $this->playerWin = false;
        $this->maxTries = 12;
        $this->numCodes = 4;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getMaxTries()
    {
        return $this->maxTries;
    }

    public function setMaxTries($maxTries)
    {
        $this->maxTries = $maxTries;
        return $this;
    }

    public function getNumCodes()
    {
        return $this->numCodes;
    }

    public function setNumCodes($numCodes)
    {
        $this->numCodes = $numCodes;
        return $this;
    }

    private function colorValid($color){
        return in_array($color, $this->listColors);
    }

    public function checkCombination($combination){
        if(!is_array($combination)){
            return false;
        }
        if(count($combination)!=$this->numCodes){
            return false;
        }
        foreach($combination as $color){
            if (!$this->colorValid($color)){
                return false;
            }
        }
        return true;
    }

    public function getGoal()
    {
        return $this->goal;
    }

    public function setGoal($goal)
    {
        if(!$this->checkCombination($goal)) {
            throw new Exception('Invalid goal combination');
        }
        $this->goal = $goal;
        return $this;
    }

    public function getOver()
    {
        return $this->over;
    }

    public function setOver($over)
    {
        $this->over = $over;
        return $this;
    }

    public function getPlayerWin()
    {
        return $this->playerWin;
    }

    public function setPlayerWin($playerWin)
    {
        $this->playerWin = $playerWin;
        return $this;
    }
}
