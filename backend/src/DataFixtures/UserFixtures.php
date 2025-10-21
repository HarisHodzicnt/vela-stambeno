<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $owner = new User();
        $owner->setEmail('owner@stambeno.ba');
        $owner->setFirstName('Haris');
        $owner->setLastName('Hodzic');
        $owner->setRoles(['ROLE_OWNER']);
        $owner->setPhone('+38761123456');
        $owner->setDateOfBirth(new \DateTime('1990-01-01'));
        $owner->setPassword($this->passwordHasher->hashPassword(
            $owner,
            'password'
        ));
        $manager->persist($owner);

        $manager->flush();
    }
}
