<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Customer\Model\CustomerInterface as CustomerComponent;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShopUserExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        private FactoryInterface $shopUserFactory,
        private FactoryInterface $customerFactory,
        private RepositoryInterface $customerGroupRepository,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

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

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('email', fn (Options $options): string => $this->faker->email)
            ->setDefault('first_name', fn (Options $options): string => $this->faker->firstName)
            ->setDefault('last_name', fn (Options $options): string => $this->faker->lastName)
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('password', 'password123')
            ->setDefault('customer_group', LazyOption::randomOneOrNull($this->customerGroupRepository, 100))
            ->setAllowedTypes('customer_group', ['null', 'string', CustomerGroupInterface::class])
            ->setNormalizer('customer_group', LazyOption::findOneBy($this->customerGroupRepository, 'code'))
            ->setDefault('gender', CustomerComponent::UNKNOWN_GENDER)
            ->setAllowedValues(
                'gender',
                [CustomerComponent::UNKNOWN_GENDER, CustomerComponent::MALE_GENDER, CustomerComponent::FEMALE_GENDER],
            )
            ->setDefault('phone_number', fn (Options $options): string => $this->faker->phoneNumber)
            ->setDefault('birthday', fn (Options $options): \DateTime => $this->faker->dateTimeThisCentury())
            ->setAllowedTypes('birthday', ['null', 'string', \DateTimeInterface::class])
            ->setNormalizer(
                'birthday',
                function (Options $options, \DateTimeInterface|string|null $value) {
                    if (is_string($value)) {
                        return \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                    }

                    return $value;
                },
            )
        ;
    }
}
