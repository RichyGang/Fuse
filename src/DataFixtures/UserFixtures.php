<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User;
        $user->setUsername('Patrick');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'sebastien'
        ));
        $user->setRoles(['ROLE_ADMIN']);

        $user1 = new User;
        $user1->setUsername('Jean');
        $user1->setPassword($this->passwordEncoder->encodePassword(
            $user1,
            'baptiste'
        ));

        $manager->persist($user);
        $manager->persist($user1);

        $manager->flush();
    }
}
