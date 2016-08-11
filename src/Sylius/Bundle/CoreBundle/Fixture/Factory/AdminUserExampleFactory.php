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

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AdminUserExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $userFactory;

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
     * @param RepositoryInterface $roleRepository
     */
    public function __construct(FactoryInterface $userFactory, RepositoryInterface $roleRepository)
    {
        $this->userFactory = $userFactory;
        $this->roleRepository = $roleRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('email', function (Options $options) {
                    return $this->faker->email;
                })
                ->setDefault('username', function (Options $options) {
                    return $this->faker->firstName.' '.$this->faker->lastName;
                })
                ->setDefault('enabled', function (Options $options) {
                    return $this->faker->boolean(90);
                })
                ->setAllowedTypes('enabled', 'bool')
                ->setDefault('password', 'password123')
                ->setDefault('api', false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var AdminUserInterface $user */
        $user = $this->userFactory->createNew();
        $user->setEmail($options['email']);
        $user->setUsername($options['username']);
        $user->setPlainPassword($options['password']);
        $user->setEnabled($options['enabled']);

        $this->addUserRole($user, 'ROLE_ADMINISTRATION_ACCESS', 'administrator');

        if ($options['api']) {
            $this->addUserRole($user, 'ROLE_API_ACCESS', 'api_administrator');
        }

        return $user;
    }

    /**
     * @param AdminUserInterface $user
     * @param string $role
     * @param string $authorizationRole
     */
    private function addUserRole(AdminUserInterface $user, $role, $authorizationRole)
    {
        $user->addRole($role);

        /** @var RoleInterface $adminRole */
        $adminRole = $this->roleRepository->findOneBy(['code' => $authorizationRole]);

        if (null !== $adminRole) {
            $user->addAuthorizationRole($adminRole);
        }
    }
}
