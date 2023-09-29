<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("admin@admin.com");
        $user->setPassword("$2y$13$.DqUi8L60xREDwGtk68JrOfD7XbCWbnlp8jwzyxcl2iFMY95FI37e"); //admin
        $user->setRoles(["ROLE_ADMIN", "ROLE_USER"]);
        $user->setUsername("admin");
        $user->setSlug("admin");
        $manager->persist($user);

        $manager->flush();
    }
}
