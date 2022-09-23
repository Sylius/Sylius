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

use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ShippingMethodInterface>
 *
 * @method static ShippingMethodInterface|Proxy createOne(array $attributes = [])
 * @method static ShippingMethodInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ShippingMethodInterface|Proxy find(object|array|mixed $criteria)
 * @method static ShippingMethodInterface|Proxy findOrCreate(array $attributes)
 * @method static ShippingMethodInterface|Proxy first(string $sortedField = 'id')
 * @method static ShippingMethodInterface|Proxy last(string $sortedField = 'id')
 * @method static ShippingMethodInterface|Proxy random(array $attributes = [])
 * @method static ShippingMethodInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ShippingMethodInterface[]|Proxy[] all()
 * @method static ShippingMethodInterface[]|Proxy[] findBy(array $attributes)
 * @method static ShippingMethodInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ShippingMethodInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ShippingMethodInterface|Proxy create(array|callable $attributes = [])
 */
interface ShippingMethodFactoryInterface extends WithCodeInterface, WithNameInterface, WithZoneInterface, WithTaxCategoryInterface, WithChannelInterface
{
    public function withDescription(string $description): self;

    public function withCategory(Proxy|ShippingCategoryInterface|string $category): self;

    public function withArchiveDate(\DateTimeInterface $archivedAt): self;
}
