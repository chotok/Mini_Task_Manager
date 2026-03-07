<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const TEST_USER_REFERENCE = 'test-user';

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    

    public function load(ObjectManager $manager): void
    {
        $testUser = new User();
        $testUser -> setEmail('junior@test.com');
        $testUser -> setPassword($this->hasher->hashPassword($testUser, 'password'));
        $manager -> persist($testUser);

        $manager->flush();

        $this->addReference(self::TEST_USER_REFERENCE, $testUser);
    }
}
