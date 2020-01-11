<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Customer\Model\CustomerInterface as CustotmerComponent;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShopUserExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $shopUserFactory;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var RepositoryInterface */
    private $customerGroupRepository;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        FactoryInterface $shopUserFactory,
        FactoryInterface $customerFactory,
        RepositoryInterface $customerGroupRepository
    ) {
        $this->shopUserFactory = $shopUserFactory;
        $this->customerFactory = $customerFactory;
        $this->customerGroupRepository = $customerGroupRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): ShopUserInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();
        $customer->setEmail($options['email']);
        $customer->setFirstName($options['first_name']);
        $customer->setLastName($options['last_name']);
        $customer->setGroup($options['customer_group']);
        $customer->setGender($options['gender']);
        $customer->setPhoneNumber($options['phone_number']);
        $customer->setBirthday($options['birthday']);

        /** @var ShopUserInterface $user */
        $user = $this->shopUserFactory->createNew();
        $user->setPlainPassword($options['password']);
        $user->setEnabled($options['enabled']);
        $user->addRole('ROLE_USER');
        $user->setCustomer($customer);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('email', function (Options $options): string {
                return $this->faker->email;
            })
            ->setDefault('first_name', function (Options $options): string {
                return $this->faker->firstName;
            })
            ->setDefault('last_name', function (Options $options): string {
                return $this->faker->lastName;
            })
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('password', 'password123')
            ->setDefault('customer_group', LazyOption::randomOneOrNull($this->customerGroupRepository, 100))
            ->setAllowedTypes('customer_group', ['null', 'string', CustomerGroupInterface::class])
            ->setNormalizer('customer_group', LazyOption::findOneBy($this->customerGroupRepository, 'code'))
            ->setDefault('gender', CustotmerComponent::UNKNOWN_GENDER)
            ->setAllowedValues(
                'gender',
                [CustotmerComponent::UNKNOWN_GENDER, CustotmerComponent::MALE_GENDER, CustotmerComponent::FEMALE_GENDER]
            )
            ->setDefault('phone_number', function (Options $options): string {
                return $this->faker->phoneNumber;
            })
            ->setDefault('birthday', function (Options $options): \DateTime {
                return $this->faker->dateTimeThisCentury();
            })
            ->setAllowedTypes('birthday', ['null', 'string', \DateTimeInterface::class])
            ->setNormalizer(
                'birthday',
                /** @param string|\DateTimeInterface|null $value */
                function (Options $options, $value) {
                    if (is_string($value)) {
                        return \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                    }

                    return $value;
                }
            )
        ;
    }
}
