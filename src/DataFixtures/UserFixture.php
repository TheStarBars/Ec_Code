<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{

    /**
     * @inheritDoc
     */
    public const USER_REFERENCE = 'user_0';

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password123');

        $manager->persist($user);
        $manager->flush();

        // Ajoutez une référence pour cet utilisateur
        $this->addReference(self::USER_REFERENCE, $user);
    }
}