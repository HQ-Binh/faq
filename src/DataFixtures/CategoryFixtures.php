<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        // Tạo các category giả
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->word);
            $manager->persist($category);

            // Lưu reference để sử dụng trong QuestionFixtures
            $this->addReference('category_' . $i, $category);
        }

        $manager->flush();
    }
}
