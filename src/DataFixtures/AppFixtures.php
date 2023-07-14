<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Channel;
use App\Entity\Message;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $allUsers =[];

        //Create 10 users
        for($i=0; $i<10; $i++) {
            $user = new User();
            $user
                ->setEmail($faker->email)
                ->setRoles([])
                ->setPassword("$2y$13\$A4vfyBhT6/8nkJGmG3HqKupc/vnjyBXTQbURKZUh1QEk1JHTD.HNC")
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
            ;

            $allUsers[] = $user;
            $manager->persist($user);
        }

        $allChannels= [];

        //Create 10 Channels
        for ($i=0; $i<10; $i++) {
            $channel = new Channel();

            $allChannels[] = $channel;

            $manager->persist($channel);
        }


        //Create 100 messages between channel 1 and 10

        for($i=0; $i<100; $i++) {

            $user = 
            $message = new Message();
            $message
                ->setContent($faker->realText(50))
                ->setDate($faker->dateTime)
                ->setAuthor($faker->randomElement($allUsers))
                ->setChannel($faker->randomElement($allChannels))
            ;

            $manager->persist($message);
        }



        $manager->flush();
    }
}
