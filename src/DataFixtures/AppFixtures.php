<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        
        for($i = 0; $i <= 30; $i++) {
            $ad = new Ad();
            $title = $faker->sentence();
            $ad->setTitle($title)
            ->setCoverImage($faker->imageUrl(1000, 350))
            ->setIntroduction($faker->paragraph(2))
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
            ->setPrice(mt_rand(40, 200))
            ->setRooms(mt_rand(1, 5));

            for ($j=1; $j < mt_rand(2, 5); $j++) { 
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                ->setCaption($faker->sentence())
                ->setAd($ad);
                $manager->persist($image);
            }
    
            $manager->persist($ad);
        }

        $manager->flush();
    }
}
