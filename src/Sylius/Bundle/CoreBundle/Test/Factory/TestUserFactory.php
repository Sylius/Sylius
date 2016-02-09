<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Test\Factory;

use Sylius\Component\Core\Test\Factory\TestUserFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class TestUserFactory implements TestUserFactoryInterface
{
    const DEFAULT_USER_EMAIL = 'john.doe@example.com';
    const DEFAULT_ADMIN_EMAIL = 'admin@example.com';
    const DEFAULT_USER_FIRST_NAME = 'John';
    const DEFAULT_USER_LAST_NAME = 'Doe';
    const DEFAULT_USER_PASSWORD = 'password123';
    const DEFAULT_USER_ROLE = 'ROLE_USER';
    const DEFAULT_ADMIN_ROLE = 'ROLE_ADMINISTRATION_ACCESS';

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var FactoryInterface
     */
    private $userFactory;

    /**
     * @param FactoryInterface $customerFactory
     * @param FactoryInterface $userFactory
     */
    public function __construct(FactoryInterface $customerFactory, FactoryInterface $userFactory)
    {
        $this->customerFactory = $customerFactory;
        $this->userFactory = $userFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create($email, $password, $firstName = self::DEFAULT_USER_FIRST_NAME, $lastName = self::DEFAULT_USER_LAST_NAME, $role = self::DEFAULT_USER_ROLE)
    {
        $customer = $this->customerFactory->createNew();

        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);

        $user = $this->userFactory->createNew();

        $user->setCustomer($customer);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->enable();
        $user->addRole($role);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function createDefault()
    {
        return $this->create(
            self::DEFAULT_USER_EMAIL,
            self::DEFAULT_USER_PASSWORD,
            self::DEFAULT_USER_FIRST_NAME,
            self::DEFAULT_USER_LAST_NAME,
            self::DEFAULT_USER_ROLE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createDefaultAdmin()
    {
        return $this->create(
            self::DEFAULT_ADMIN_EMAIL,
            self::DEFAULT_USER_PASSWORD,
            self::DEFAULT_USER_FIRST_NAME,
            self::DEFAULT_USER_LAST_NAME,
            self::DEFAULT_ADMIN_ROLE
        );
    }
}
