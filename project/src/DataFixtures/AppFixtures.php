<?php

namespace App\DataFixtures;

use App\Entity\ExpenseReport;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $admin = (new Role())
            ->setTitle('admin');
        $employee = (new Role())
            ->setTitle('employee');

        $manager->persist($admin);
        $manager->persist($employee);

        $users = [];

        for ($i = 0; $i < 10; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();

            $user = (new User())
                ->setMail($firstName.'.'.$lastName.'@gmail.com')
                ->setPassword('password')
                ->setFullname($firstName.' '.$lastName)
                ->setRole($i % 2 == 0 ? $admin : $employee)
                ->setIsDeleted(false);

            $manager->persist($user);

            $users[] = $user;
        }

        for ($i = 0; $i < 10; $i++) {
            $randomUser = $users[array_rand($users)];

            $expenseReport = (new ExpenseReport())
                ->setOwner($randomUser)
                ->setReason($faker->sentence(3))
                ->setStatus(ExpenseReport::STATUS_EN_COURS)
                ->setCost($faker->randomFloat(2, 0, 100));

            $manager->persist($expenseReport);
        }

        $manager->flush();
    }
}
