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

use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserFactory implements UserFactoryInterface
{
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
    public function create($firstName, $lastName, $email, $password)
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

        return $user;
    }
}
