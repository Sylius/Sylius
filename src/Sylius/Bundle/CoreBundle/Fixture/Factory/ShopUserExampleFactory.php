<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ShopUserExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $userFactory;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $userFactory
     * @param FactoryInterface $customerFactory
     */
    public function __construct(
        FactoryInterface $userFactory,
        FactoryInterface $customerFactory
    ) {
        $this->userFactory = $userFactory;
        $this->customerFactory = $customerFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('email', function (Options $options) {
                    return $this->faker->email;
                })
                ->setDefault('first_name', function (Options $options) {
                    return $this->faker->firstName;
                })
                ->setDefault('last_name', function (Options $options) {
                    return $this->faker->lastName;
                })
                ->setDefault('enabled', function (Options $options) {
                    return $this->faker->boolean(90);
                })
                ->setAllowedTypes('enabled', 'bool')
                ->setDefault('password', 'password123')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();
        $customer->setEmail($options['email']);
        $customer->setFirstName($options['first_name']);
        $customer->setLastName($options['last_name']);

        /** @var ShopUserInterface $user */
        $user = $this->userFactory->createNew();
        $user->setPlainPassword($options['password']);
        $user->setEnabled($options['enabled']);
        $user->addRole('ROLE_USER');
        $user->setCustomer($customer);

        return $user;
    }
}
