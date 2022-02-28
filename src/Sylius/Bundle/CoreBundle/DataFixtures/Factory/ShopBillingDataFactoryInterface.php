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

use Sylius\Component\Core\Model\ShopBillingDataInterface;
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
interface ShopBillingDataFactoryInterface
{
    public function withCompany(string $company): self;

    public function withTaxId(string $taxId): self;

    public function withCountryCode(string $countryCode): self;

    public function withStreet(string $street): self;

    public function withCity(string $city): self;

    public function withPostcode(string $postcode): self;
}
