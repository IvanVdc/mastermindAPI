<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use FOS\RestBundle\Exception\InvalidParameterException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity to save a turn info
 *
 * @ApiResource
 * @ORM\Entity
 */
class Turn
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="turns")
     */
    private $game;

    /**
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @ORM\Column(type="string")
     */
    private $guess;

    /**
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @ORM\Column(type="string")
     */
    private $result;

    public function getId()
    {
        return $this->id;
    }

    public function getGame()
    {
        return $this->game;
    }

    public function setGame($game)
    {
        $this->game = $game;
        return $this;
    }

    public function getGuess()
    {
        return $this->guess;
    }

    public function setGuess($guess)
    {
        $this->guess = $guess;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }
}
