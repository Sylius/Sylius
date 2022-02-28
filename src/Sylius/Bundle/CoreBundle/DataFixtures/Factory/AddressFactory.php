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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<AddressInterface>
 *
 * @method static AddressInterface|Proxy createOne(array $attributes = [])
 * @method static AddressInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static AddressInterface|Proxy find(object|array|mixed $criteria)
 * @method static AddressInterface|Proxy findOrCreate(array $attributes)
 * @method static AddressInterface|Proxy first(string $sortedField = 'id')
 * @method static AddressInterface|Proxy last(string $sortedField = 'id')
 * @method static AddressInterface|Proxy random(array $attributes = [])
 * @method static AddressInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static AddressInterface[]|Proxy[] all()
 * @method static AddressInterface[]|Proxy[] findBy(array $attributes)
 * @method static AddressInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static AddressInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method AddressInterface|Proxy create(array|callable $attributes = [])
 */
class AddressFactory extends ModelFactory implements AddressFactoryInterface
{
    public function __construct(
        private FactoryInterface $addressFactory,
        private CountryFactoryInterface $countryFactory,
        private ShopUserFactoryInterface $shopUserFactory
    ) {
        parent::__construct();
    }

    public function withFirstName(string $firstName): self
    {
        return $this->addState(['first_name' => $firstName]);
    }

    public function withLastName(string $lastName): self
    {
        return $this->addState(['last_name' => $lastName]);
    }

    public function withPhoneNumber(string $phoneNumber): self
    {
        return $this->addState(['phone_number' => $phoneNumber]);
    }

    public function withCompany(string $company): self
    {
        return $this->addState(['company' => $company]);
    }

    public function withStreet(string $street): self
    {
        return $this->addState(['street' => $street]);
    }

    public function withCity(string $city): self
    {
        return $this->addState(['city' => $city]);
    }

    public function withPostcode(string $postcode): self
    {
        return $this->addState(['postcode' => $postcode]);
    }

    public function withCountryCode(string $countryCode): self
    {
        return $this->addState(['country_code' => $countryCode]);
    }

    public function withProvinceName(string $provinceName): self
    {
        return $this->addState(['province_name' => $provinceName]);
    }

    public function withProvinceCode(string $provinceCode): self
    {
        return $this->addState(['province_code' => $provinceCode]);
    }

    public function withCustomer(Proxy|CustomerInterface|string $customer): self
    {
        return $this->addState(function () use ($customer): array {
            if (is_string($customer)) {
                $customer = $this->shopUserFactory::randomOrCreate(['email' => $customer])->getCustomer();
            }

            return ['customer' => $customer];
        });
    }

    protected function getDefaults(): array
    {
        return [
            'first_name' => self::faker()->firstName(),
            'last_name' => self::faker()->lastName(),
            'phone_number' => self::faker()->boolean() ? self::faker()->phoneNumber() : null,
            'company' => self::faker()->boolean() ? self::faker()->company() : null,
            'street' => self::faker()->streetAddress(),
            'city' => self::faker()->city(),
            'postcode' => self::faker()->postcode(),
            'country_code' => $this->countryFactory::randomOrCreate()->getCode(),
            'province_name' => null,
            'province_code' => null,
            'customer' => $this->shopUserFactory::randomOrCreate()->getCustomer(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): AddressInterface {
                /** @var AddressInterface $address */
                $address = $this->addressFactory->createNew();
                $address->setFirstName($attributes['first_name']);
                $address->setLastName($attributes['last_name']);
                $address->setPhoneNumber($attributes['phone_number']);
                $address->setCompany($attributes['company']);
                $address->setStreet($attributes['street']);
                $address->setCity($attributes['city']);
                $address->setPostcode($attributes['postcode']);
                $address->setCountryCode($attributes['country_code']);
                $address->setProvinceName($attributes['province_name']);
                $address->setProvinceCode($attributes['province_code']);
                $address->setCustomer($attributes['customer']);

                return $address;
            })
        ;
    }

    protected static function getClass(): string
    {
        return Address::class;
    }
}
