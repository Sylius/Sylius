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
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Core\Model\UserInterface;

/**
 * User fixtures.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadUsersData extends DataFixture
{
    private $usernames = [];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->createUser(
            'sylius@example.com',
            'sylius',
            true,
            ['ROLE_USER', 'ROLE_ADMINISTRATION_ACCESS']
        );
        $user->addAuthorizationRole($this->get('sylius.repository.role')->findOneBy(['code' => 'administrator']));

        $manager->persist($user);
        $manager->flush();

        $this->setReference('Sylius.User-Administrator', $user);

        for ($i = 1; $i <= 15; ++$i) {
            $username = $this->faker->username;

            while (isset($this->usernames[$username])) {
                $username = $this->faker->username;
            }

            $user = $this->createUser(
                $username.'@example.com',
                $username,
                $this->faker->boolean()
            );

            $user->setCreatedAt($this->faker->dateTimeThisMonth);

            $manager->persist($user);
            $this->usernames[$username] = true;

            $this->setReference('Sylius.User-'.$i, $user);
            $this->setReference('Sylius.Customer-'.$i, $user->getCustomer());
        }

        $customer = $this->getCustomerFactory()->createNew();
        $customer->setFirstname($this->faker->firstName);
        $customer->setLastname($this->faker->lastName);
        $customer->setEmail('customer@email.com');
        $manager->persist($customer);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool   $enabled
     * @param array  $roles
     * @param string $currency
     *
     * @return UserInterface
     */
    protected function createUser($email, $password, $enabled = true, array $roles = ['ROLE_USER'], $currency = 'EUR')
    {
        $canonicalizer = $this->get('sylius.user.canonicalizer');

        /* @var $user UserInterface */
        $user = $this->getUserFactory()->createNew();
        $customer = $this->getCustomerFactory()->createNew();
        $customer->setFirstname($this->faker->firstName);
        $customer->setLastname($this->faker->lastName);
        $customer->setCurrency($currency);
        $user->setCustomer($customer);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setUsernameCanonical($canonicalizer->canonicalize($user->getUsername()));
        $user->setEmailCanonical($canonicalizer->canonicalize($user->getEmail()));
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setEnabled($enabled);

        $this->get('sylius.user.password_updater')->updatePassword($user);

        return $user;
    }
}
