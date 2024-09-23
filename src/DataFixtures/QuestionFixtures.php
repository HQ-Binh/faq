<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class QuestionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Tạo các question giả
        for ($i = 0; $i < 50; $i++) {
            $question = new Question();
            $question->setContent($faker->paragraph);

            // Set reference cho category
            $category = $this->getReference('category_' . rand(0, 9));
            $question->setCategory($category);

            $manager->persist($question);

            // Lưu reference để sử dụng trong AnswerFixtures
            $this->addReference('question_' . $i, $question);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
