<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class UserFixture extends AbstractResourceFixture
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
    private $currencyRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @param FactoryInterface $userFactory
     * @param ObjectManager $userManager
     * @param FactoryInterface $customerFactory
     * @param RepositoryInterface $currencyRepository
     */
    public function __construct(
        FactoryInterface $userFactory,
        ObjectManager $userManager,
        FactoryInterface $customerFactory,
        RepositoryInterface $currencyRepository
    ) {
        parent::__construct($userManager, 'users', 'email');

        $this->userFactory = $userFactory;
        $this->customerFactory = $customerFactory;
        $this->currencyRepository = $currencyRepository;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResource(array $options)
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();
        $customer->setEmail($options['email']);
        $customer->setFirstName($options['first_name']);
        $customer->setLastName($options['last_name']);
        $customer->setCurrencyCode($options['currency_code']);

        /** @var UserInterface $user */
        $user = $this->userFactory->createNew();
        $user->setPlainPassword($options['password']);
        $user->setEnabled($options['enabled']);
        $user->addRole('ROLE_USER');

        if ($options['admin']) {
            $user->addRole('ROLE_ADMINISTRATION_ACCESS');
        }

        $user->setCustomer($customer);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('first_name')->cannotBeEmpty()->end()
                ->scalarNode('last_name')->cannotBeEmpty()->end()
                ->scalarNode('currency_code')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->booleanNode('admin')->end()
                ->scalarNode('password')->cannotBeEmpty()->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceOptionsResolver(array $options, OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired(['email'])
            ->setDefault('first_name', function (Options $options) {
                return $this->faker->firstName;
            })
            ->setDefault('last_name', function (Options $options) {
                return $this->faker->lastName;
            })
            ->setDefault('currency_code', null)
            ->setAllowedTypes('currency_code', ['null', 'string'])
            ->setNormalizer('currency_code', function (Options $options, $currencyCode) {
                $currencyProvider = static::createResourceNormalizer($this->currencyRepository);

                /** @var CurrencyInterface $currency */
                $currency = $currencyProvider($options, $currencyCode);

                return $currency->getCode();
            })
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('admin', false)
            ->setAllowedTypes('admin', 'bool')
            ->setDefault('password', function (Options $options) {
                return $this->faker->password;
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateResourcesOptions($amount)
    {
        $resourcesOptions = [];
        for ($i = 0; $i < $amount; ++$i) {
            $resourcesOptions[] = ['email' => $this->faker->email];
        }

        return $resourcesOptions;
    }
}
