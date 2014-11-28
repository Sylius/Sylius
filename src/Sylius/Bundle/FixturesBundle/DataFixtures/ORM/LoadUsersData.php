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
use Sylius\Bundle\CoreBundle\Manager\UserManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\UserInterface;

/**
 * User fixtures.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadUsersData extends DataFixture
{
    private $usernames = array();

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $rbacInitializer = $this->get('sylius.rbac.initializer');
        $rbacInitializer->initialize();

        list($user, $customer) = $this->createUser(
            'sylius@example.com',
            'sylius',
            true,
            array('ROLE_SYLIUS_ADMIN')
        );
        $user->addAuthorizationRole($this->get('sylius.repository.role')->findOneBy(array('code' => 'administrator')));

        $this->getUserManager()->updateUser($user);

        $this->setReference('Sylius.Customer-Administrator', $customer);
        $this->setReference('Sylius.User-Administrator', $user);

        for ($i = 1; $i <= 200; $i++) {
            $username = $this->faker->username;

            while (isset($this->usernames[$username])) {
                $username = $this->faker->username;
            }

            list($user, $customer) = $this->createUser(
                $username.'@example.com',
                $username,
                $this->faker->boolean()
            );

            $user->setCreatedAt($this->faker->dateTimeThisMonth);

            $this->getUserManager()->updateUser($user, false);
            $this->usernames[$username] = true;

            $this->setReference('Sylius.Customer-'.$i, $customer);
            $this->setReference('Sylius.User-'.$i, $user);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
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
    protected function createUser($email, $password, $enabled = true, array $roles = array('ROLE_USER'), $currency = 'EUR')
    {
        /** @var $customer CustomerInterface */
        $customer = $this->getCustomerRepository()->createNew();
        $customer->setFirstname($this->faker->firstName);
        $customer->setLastname($this->faker->lastName);
        $customer->setCurrency($currency);

        /** @var $user UserInterface */
        $user = $this->getUserManager()->createUser($customer);
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setEnabled($enabled);
        $user->setEmail($email);

        return array($user, $customer);
    }

    /**
     * @return UserManager
     */
    protected function getUserManager()
    {
        return $this->get('sylius.user_manager');
    }
}
