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
    public function __construct($maxTries=12, $numCodes=4)
    {
        $this->over = false;
        $this->playerWin = false;
        $this->maxTries = $maxTries;
        $this->numCodes = $numCodes;
        $goal = array();
        foreach (range(1, $numCodes) as $número) {
            $goal[]=$this->listColors[rand(0,count($this->listColors)-1)];
        }
        $this->goal = json_encode($goal);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getListColors()
    {
        return $this->listColors;
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

    public function getNumCodes()
    {
        return $this->numCodes;
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

    public function getResult($guess){
        $result = array(
            'WHITE' => 0,
            'BLACK' => 0
        );
        $init_goal = $this->getGoal();
        foreach (range(0, $this->numCodes-1) as $i) {
            $color = $guess[$i];
            if ($color === $this->getGoal()[$i]) {
                $result['BLACK']++;
                $init_goal[$i] = 'NONE';
                $guess[$i] = 'DONE';
            }
        }
        foreach (range(0, $this->numCodes-1) as $i) {
            $color = $guess[$i];
            $exists = false;
            $i = 0;
            while (!$exists && $i<$this->numCodes) {
                if ($color === $init_goal[$i]) {
                    $result['WHITE']++;
                    $init_goal[$i] = 'NONE';
                    $exists = true;
                }
                $i++;
            }
        }
        return $result;
    }

    public function getGoal()
    {
        return json_decode($this->goal);
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
