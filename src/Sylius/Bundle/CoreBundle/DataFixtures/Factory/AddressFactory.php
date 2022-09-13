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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues\AddressFactoryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer\AddressFactoryTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater\AddressFactoryUpdaterInterface;
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
class AddressFactory extends ModelFactory implements AddressFactoryInterface, FactoryWithModelClassAwareInterface
{
    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $addressFactory,
        private AddressFactoryDefaultValuesInterface $factoryDefaultValues,
        private AddressFactoryTransformerInterface $factoryTransformer,
        private AddressFactoryUpdaterInterface $factoryUpdater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
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
            return ['customer' => $customer];
        });
    }

    protected function getDefaults(): array
    {
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
    }

    protected function update(AddressInterface $address): void
    {
        $this->factoryUpdater->update($address);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
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
            ->afterInstantiate(function(AddressInterface $address): void {
                $this->update($address);
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Address::class;
    }
}
