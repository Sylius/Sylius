<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * User fixtures.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadUsersData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->createUser(
            'sylius@example.com',
            'sylius',
            true,
            array('ROLE_SYLIUS_ADMIN')
        );

        $manager->persist($user);
        $manager->flush();

        $this->setReference('Sylius.User-Administrator', $user);

        for ($i = 1; $i <= 15; $i++) {
            $username = $this->faker->username;

            $user = $this->createUser(
                $username.'@example.com',
                $username,
                $this->faker->boolean()
            );

            $manager->persist($user);

            $this->setReference('Sylius.User-'.$i, $user);
        }

        $manager->flush();
    }

    protected function createUser($email, $password, $enabled = true, array $roles = array('ROLE_USER'), $currency = 'EUR')
    {
        $user = $this->getUserRepository()->createNew();

        $user->setFirstname($this->faker->firstName);
        $user->setLastname($this->faker->lastName);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setCurrency($currency);
        $user->setEnabled($enabled);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
