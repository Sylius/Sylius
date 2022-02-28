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

use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ShopBillingDataInterface>
 *
 * @method static ShopBillingDataInterface|Proxy createOne(array $attributes = [])
 * @method static ShopBillingDataInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ShopBillingDataInterface|Proxy find(object|array|mixed $criteria)
 * @method static ShopBillingDataInterface|Proxy findOrCreate(array $attributes)
 * @method static ShopBillingDataInterface|Proxy first(string $sortedField = 'id')
 * @method static ShopBillingDataInterface|Proxy last(string $sortedField = 'id')
 * @method static ShopBillingDataInterface|Proxy random(array $attributes = [])
 * @method static ShopBillingDataInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ShopBillingDataInterface[]|Proxy[] all()
 * @method static ShopBillingDataInterface[]|Proxy[] findBy(array $attributes)
 * @method static ShopBillingDataInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ShopBillingDataInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ShopBillingDataInterface|Proxy create(array|callable $attributes = [])
 */
class ShopBillingDataFactory extends ModelFactory implements ShopBillingDataFactoryInterface
{
    public function __construct(private FactoryInterface $shopBillingDataFactory)
    {
        parent::__construct();
    }

    public function withCompany(string $company): self
    {
        return $this->addState(['company' => $company]);
    }

    public function withTaxId(string $taxId): self
    {
        return $this->addState(['tax_id' => $taxId]);
    }

    public function withCountryCode(string $countryCode): self
    {
        return $this->addState(['country_code' => $countryCode]);
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

    protected function getDefaults(): array
    {
        return [
            'company' => self::faker()->company(),
            'tax_id' => null,
            'country_code' => self::faker()->countryCode(),
            'street' => self::faker()->streetAddress(),
            'city' => self::faker()->city(),
            'postcode' => self::faker()->postcode(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): ShopBillingDataInterface {
                /** @var ShopBillingDataInterface $shopBillingData */
                $shopBillingData = $this->shopBillingDataFactory->createNew();

                $shopBillingData->setCompany($attributes['company']);
                $shopBillingData->setTaxId($attributes['tax_id']);
                $shopBillingData->setCountryCode($attributes['country_code']);
                $shopBillingData->setStreet($attributes['street']);
                $shopBillingData->setCity($attributes['city']);
                $shopBillingData->setPostcode($attributes['postcode']);

                return $shopBillingData;
            })
        ;
    }

    protected static function getClass(): string
    {
        return ShopBillingData::class;
    }
}
