<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Task;
use App\Entity\User;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i<=5; $i++) {
            $testTask = new Task();
            $testTask -> setTitle("Task {$i}");
            $testTask -> setDescription("Descriere {$i}");
            $testTask -> setCreatedAt(new \DateTime());
            $testTask -> setUpdatedAt(new \DateTime());
            $testTask -> setStatus((random_int(0,1)));
            $testTask -> setOwner($this->getReference(UserFixtures::TEST_USER_REFERENCE, User::class));
            $manager -> persist($testTask);

            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
