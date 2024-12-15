<?php

namespace App\DataFixtures;

use App\Entity\Player;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlayerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $player1 = new Player();
        $player1->setName('Real Player');
        $player1->setBot(false);
        $player1->setCurrentCell(1); // Начальная клетка

        $player2 = new Player();
        $player2->setName('Bot Player');
        $player2->setBot(true);
        $player2->setCurrentCell(1); // Начальная клетка

        $manager->persist($player1);
        $manager->persist($player2);

        $manager->flush();
    }
}
