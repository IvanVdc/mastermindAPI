<?php

namespace App\DataFixtures;

use App\Entity\Turn;
use App\Entity\User;
use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'password'
        ));
        $user->setUsername('test');
        $user->setApiToken('test');
        $manager->persist($user);


        $user2 = new User();
        $user2->setPassword($this->passwordEncoder->encodePassword(
            $user2,
            'password2'
        ));
        $user2->setUsername('test2');
        $user2->setApiToken('test2');
        $manager->persist($user2);

        $game = new Game();
        $game->setUser($user2);
        $game->setOver(true);
        $game->setPlayerWin(true);
        $manager->persist($game);

        $turn1 = new Turn();
        $turn1->setGame($game);
        $turn1->setGuess($game->getGoal());
        $turn1->setResult(array());
        $manager->persist($turn1);

        $game2 = new Game(2,4);
        $game2->setUser($user2);
        $game2->setOver(true);
        $manager->persist($game2);

        $turn2 = new Turn();
        $turn2->setGame($game2);
        $turn2->setGuess(array());
        $turn2->setResult(array());
        $manager->persist($turn2);

        $turn3 = new Turn();
        $turn3->setGame($game2);
        $turn3->setGuess(array());
        $turn3->setResult(array());
        $manager->persist($turn3);

        $game3 = new Game();
        $game3->setUser($user2);
        $manager->persist($game3);

        $manager->flush();
    }
}
