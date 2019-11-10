<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;

    }
    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        $users = [];
        $genres = ['male', 'female'];

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser
            ->setFirstName('Nicolas')
            ->setLastName('Lair')
            ->setEmail('nlair@symfony.com')
            ->setHash($this->encoder->encodePassword($adminUser, 'password'))
            ->setPicture('https://randomuser.me/api/portraits/men/1.jpg')
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
            ->addUserRole($adminRole);

        $manager->persist($adminUser);
        
        // Nous gérons les utilisateurs
        for ($i=1; $i < 10; $i++) { 
            $user = new User();
            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';       
            $picture .= ($genre === 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user
                ->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastname)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setHash($hash)
                ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        // Nous gérons les annonces.
        for($i = 0; $i <= 30; $i++) {
            $ad = new Ad();
            $title = $faker->sentence();
            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
            ->setCoverImage($faker->imageUrl(1000, 350))
            ->setIntroduction($faker->paragraph(2))
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
            ->setPrice(mt_rand(40, 200))
            ->setRooms(mt_rand(1, 5))
            ->setAuthor($user);

            for ($j=1; $j < mt_rand(2, 5); $j++) { 
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                ->setCaption($faker->sentence())
                ->setAd($ad);
                $manager->persist($image);
            }

            // Gestion des réservations
            for ($j=1; $j <= mt_rand(0, 10) ; $j++) { 
                $booking = new Booking();
                $createAt = $faker->dateTimeBetween('-6 months');

                $startDate = $faker->dateTimeBetween('-3 months');
                $duration = mt_rand(3, 10);
                $endDate = (clone $startDate)->modify("+$duration days");

                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users) - 1)];

                $booking
                    ->setBooker($booker)
                    ->setAd($ad)
                    ->setStartDate($startDate)
                    ->setEnDate($endDate)
                    ->setCreatedAt($createAt)
                    ->setAmount($amount);
                
                $manager->persist($booking);
            }
    
            $manager->persist($ad);
        }

        $manager->flush();
    }
}
