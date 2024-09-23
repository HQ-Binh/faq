<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AnswerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Tạo các answer giả
        for ($i = 0; $i < 50; $i++) {
            $answer = new Answer();
            $answer->setContent($faker->paragraph);

            // Set reference cho question
            $question = $this->getReference('question_' . $i);
            $answer->setQuestion($question);

            $manager->persist($answer);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            QuestionFixtures::class,
        ];
    }
}

