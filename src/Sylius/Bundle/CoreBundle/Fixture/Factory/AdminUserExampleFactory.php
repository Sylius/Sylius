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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class AdminUserExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $userFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @var string
     */
    private $localeCode;

    /**
     * @param FactoryInterface $userFactory
     * @param string $localeCode
     */
    public function __construct(FactoryInterface $userFactory, $localeCode)
    {
        $this->userFactory = $userFactory;
        $this->localeCode = $localeCode;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
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
        $user->addRole('ROLE_ADMINISTRATION_ACCESS');
        $user->setLocaleCode($options['locale_code']);

        if (isset($options['first_name'])) {
            $user->setFirstName($options['first_name']);
        }
        if (isset($options['last_name'])) {
            $user->setLastName($options['last_name']);
        }

        if ($options['api']) {
            $user->addRole('ROLE_API_ACCESS');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('email', function (Options $options) {
                return $this->faker->email;
            })
            ->setDefault('username', function (Options $options) {
                return $this->faker->firstName.' '.$this->faker->lastName;
            })
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('password', 'password123')
            ->setDefault('locale_code', $this->localeCode)
            ->setDefault('api', false)
            ->setDefined('first_name')
            ->setDefined('last_name')
        ;
    }
}
