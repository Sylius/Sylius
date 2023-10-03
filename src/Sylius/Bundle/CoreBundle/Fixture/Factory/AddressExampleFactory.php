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

use Doctrine\Common\Collections\Collection;
use Faker\Factory;
use Faker\Generator;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

class AddressExampleFactory extends AbstractExampleFactory
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        private FactoryInterface $addressFactory,
        private RepositoryInterface $countryRepository,
        private RepositoryInterface $customerRepository,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('first_name', fn (Options $options): string => $this->faker->firstName)
            ->setDefault('last_name', fn (Options $options): string => $this->faker->lastName)
            ->setDefault('phone_number', fn (Options $options): ?string => random_int(1, 100) > 50 ? $this->faker->phoneNumber : null)
            ->setDefault('company', fn (Options $options): ?string => random_int(1, 100) > 50 ? $this->faker->company : null)
            ->setDefault('street', fn (Options $options): string => $this->faker->streetAddress)
            ->setDefault('city', fn (Options $options): string => $this->faker->city)
            ->setDefault('postcode', fn (Options $options): string => $this->faker->postcode)
            ->setDefault('country_code', function (Options $options): string {
                /** @var CountryInterface[] $countries */
                $countries = $this->countryRepository->findAll();
                shuffle($countries);

                return array_pop($countries)->getCode();
            })
            ->setAllowedTypes('country_code', ['string'])
            ->setDefault('province_name', null)
            ->setAllowedTypes('province_name', ['null', 'string'])
            ->setDefault('province_code', null)
            ->setAllowedTypes('province_code', ['null', 'string'])
            ->setDefault('customer', LazyOption::randomOne($this->customerRepository))
            ->setAllowedTypes('customer', ['string', CustomerInterface::class, 'null'])
            ->setNormalizer('customer', LazyOption::getOneBy($this->customerRepository, 'email'))
        ;
    }

    public function create(array $options = []): AddressInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setFirstName($options['first_name']);
        $address->setLastName($options['last_name']);
        $address->setPhoneNumber($options['phone_number']);
        $address->setCompany($options['company']);
        $address->setStreet($options['street']);
        $address->setCity($options['city']);
        $address->setPostcode($options['postcode']);

        $this->assertCountryCodeIsValid($options['country_code']);
        $address->setCountryCode($options['country_code']);

        $this->resolveCountryProvince($options, $address);

        if (isset($options['customer'])) {
            $options['customer']->addAddress($address);
        }

        return $address;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertCountryCodeIsValid(string $code): void
    {
        $country = $this->countryRepository->findOneBy(['code' => $code]);
        Assert::notNull($country, sprintf('Trying to create address with invalid country code: "%s"', $code));
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertProvinceCodeIsValid(string $provinceCode, string $countryCode): void
    {
        /** @var CountryInterface $country */
        $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

        foreach ($country->getProvinces() as $province) {
            if ($province->getCode() === $provinceCode) {
                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('Provided province code is not valid for "%s"', $country->getName()));
    }

    private function provideProvince(array $options, AddressInterface $address): void
    {
        /** @var CountryInterface $country */
        $country = $this->countryRepository->findOneBy(['code' => $options['country_code']]);

        if ($country->hasProvinces()) {
            $address->setProvinceCode($this->getProvinceCode($country->getProvinces(), $options['province_name']));

            return;
        }

        $address->setProvinceName($options['province_name']);
    }

    /**
     * @param Collection<array-key, ProvinceInterface> $provinces
     *
     * @throws \InvalidArgumentException
     */
    private function getProvinceCode(Collection $provinces, string $provinceName): string
    {
        /** @var ProvinceInterface $province */
        foreach ($provinces as $province) {
            if ($province->getName() === $provinceName) {
                return $province->getCode();
            }
        }

        throw new \InvalidArgumentException(sprintf('Country has defined provinces, but %s is not one of them', $provinceName));
    }

    private function resolveCountryProvince(array $options, AddressInterface $address): void
    {
        if (null !== $options['province_code']) {
            $this->assertProvinceCodeIsValid($options['province_code'], $options['country_code']);
            $address->setProvinceCode($options['province_code']);

            return;
        }

        if (null !== $options['province_name']) {
            $this->provideProvince($options, $address);
        }
    }
}
