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
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class UserExampleFactory implements ExampleFactoryInterface
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
     * @var RepositoryInterface
     */
    private $roleRepository;

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
     * @param RepositoryInterface $roleRepository
     */
    public function __construct(
        FactoryInterface $userFactory,
        FactoryInterface $customerFactory,
        RepositoryInterface $roleRepository
    ) {
        $this->userFactory = $userFactory;
        $this->customerFactory = $customerFactory;
        $this->roleRepository = $roleRepository;

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
                ->setDefault('admin', false)
                ->setAllowedTypes('admin', 'bool')
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

        /** @var UserInterface $user */
        $user = $this->userFactory->createNew();
        $user->setPlainPassword($options['password']);
        $user->setEnabled($options['enabled']);
        $user->addRole('ROLE_USER');

        if ($options['admin']) {
            $user->addRole('ROLE_ADMINISTRATION_ACCESS');

            /** @var RoleInterface $adminRole */
            $adminRole = $this->roleRepository->findOneBy(['code' => 'administrator']);

            if (null !== $adminRole) {
                $user->addAuthorizationRole($adminRole);
            }
        }

        $user->setCustomer($customer);

        return $user;
    }
}
