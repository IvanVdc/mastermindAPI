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
        $user->setApiToken('test_token');
        $manager->persist($user);


        $user2 = new User();
        $user2->setPassword($this->passwordEncoder->encodePassword(
            $user2,
            'password2'
        ));
        $user2->setUsername('test2');
        $user2->setApiToken('test_token2');
        $manager->persist($user2);

        $game = new Game();
        $game->setUser($user2);
        $manager->persist($game);

        $game2 = new Game();
        $game2->setUser($user2);
        $game2->setOver(true);
        $game2->setPlayerWin(true);
        $manager->persist($game2);

        $turn1 = new Turn();
        $turn1->setGame($game2);
        $turn1->setGuess($game2->getGoal());
        $turn1->setResult(array());
        $manager->persist($turn1);

        $game3 = new Game(2,4);
        $game3->setUser($user2);
        $game3->setOver(true);
        $manager->persist($game3);

        $turn2 = new Turn();
        $turn2->setGame($game3);
        $turn2->setGuess(array());
        $turn2->setResult(array());
        $manager->persist($turn2);

        $turn3 = new Turn();
        $turn3->setGame($game3);
        $turn3->setGuess(array());
        $turn3->setResult(array());
        $manager->persist($turn3);

        $manager->flush();
    }
}
