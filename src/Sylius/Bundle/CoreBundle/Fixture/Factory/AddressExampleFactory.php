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

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class AddressExampleFactory extends AbstractExampleFactory
{
    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $addressFactory
     * @param RepositoryInterface $countryRepository
     * @param RepositoryInterface $customerRepository
     */
    public function __construct(
        FactoryInterface $addressFactory,
        RepositoryInterface $countryRepository,
        RepositoryInterface $customerRepository
    ) {
        $this->addressFactory = $addressFactory;
        $this->countryRepository = $countryRepository;
        $this->customerRepository = $customerRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('first_name', function (Options $options) {
                return $this->faker->firstName;
            })
            ->setDefault('last_name', function (Options $options) {
                return $this->faker->lastName;
            })
            ->setDefault('phone_number', function (Options $options) {
                return mt_rand(1, 100) > 50 ? $this->faker->phoneNumber : null;
            })
            ->setDefault('company', function (Options $options) {
                return mt_rand(1, 100) > 50 ? $this->faker->company : null;
            })
            ->setDefault('street', function (Options $options) {
                return $this->faker->streetAddress;
            })
            ->setDefault('city', function (Options $options) {
                return $this->faker->city;
            })
            ->setDefault('postcode', function (Options $options) {
                return $this->faker->postcode;
            })
            ->setDefault('country_code', function (Options $options) {
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
            ->setNormalizer('customer', LazyOption::findOneBy($this->customerRepository, 'email'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
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
     * @param string $code
     */
    private function assertCountryCodeIsValid($code)
    {
        $country = $this->countryRepository->findOneBy(['code' => $code]);
        Assert::notNull($country);
    }

    /**
     * @param string $provinceCode
     * @param string $countryCode
     */
    private function assertProvinceCodeIsValid($provinceCode, $countryCode)
    {
        $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

        /** @var ProvinceInterface $province */
        foreach ($country->getProvinces() as $province) {
            if ($province->getCode() === $provinceCode) {
                return;
            }
        }
        throw new \InvalidArgumentException('Provided province code is not valid for "%s"', $country->getName());
    }

    /**
     * @param array $options
     * @param AddressInterface $address
     *
     * @return string
     */
    private function provideProvince(array $options, AddressInterface $address)
    {
        /** @var Country $country */
        $country = $this->countryRepository->findOneBy(['code' => $options['country_code']]);

        if ($country->hasProvinces()) {
            $address->setProvinceCode($this->getProvinceCode($country->getProvinces(), $options['province_name']));

            return;
        }

        $address->setProvinceName($options['province_name']);
    }

    /**
     * @param Collection $provinces
     * @param string $provinceName
     *
     * @return string
     */
    private function getProvinceCode(Collection $provinces, $provinceName)
    {
        /** @var ProvinceInterface $province */
        foreach ($provinces as $province) {
            if ($province->getName() === $provinceName) {
                return $province->getCode();
            }
        }

        throw new \InvalidArgumentException(sprintf('Country has defined provinces, but %s is not one of them', $provinceName));
    }

    /**
     * @param array $options
     * @param AddressInterface $address
     */
    private function resolveCountryProvince(array $options, AddressInterface $address)
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
