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

use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ShippingCategoryInterface>
 *
 * @method static ShippingCategoryInterface|Proxy createOne(array $attributes = [])
 * @method static ShippingCategoryInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ShippingCategoryInterface|Proxy find(object|array|mixed $criteria)
 * @method static ShippingCategoryInterface|Proxy findOrCreate(array $attributes)
 * @method static ShippingCategoryInterface|Proxy first(string $sortedField = 'id')
 * @method static ShippingCategoryInterface|Proxy last(string $sortedField = 'id')
 * @method static ShippingCategoryInterface|Proxy random(array $attributes = [])
 * @method static ShippingCategoryInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ShippingCategoryInterface[]|Proxy[] all()
 * @method static ShippingCategoryInterface[]|Proxy[] findBy(array $attributes)
 * @method static ShippingCategoryInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ShippingCategoryInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ShippingCategoryInterface|Proxy create(array|callable $attributes = [])
 */
interface ShippingCategoryFactoryInterface extends WithCodeInterface, WithNameInterface
{
    public function withDescription(string $description): self;
}
