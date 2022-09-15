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

use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ProductAssociationTypeInterface>
 *
 * @method static ProductAssociationTypeInterface|Proxy createOne(array $attributes = [])
 * @method static ProductAssociationTypeInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductAssociationTypeInterface|Proxy find(object|array|mixed $criteria)
 * @method static ProductAssociationTypeInterface|Proxy findOrCreate(array $attributes)
 * @method static ProductAssociationTypeInterface|Proxy first(string $sortedField = 'id')
 * @method static ProductAssociationTypeInterface|Proxy last(string $sortedField = 'id')
 * @method static ProductAssociationTypeInterface|Proxy random(array $attributes = [])
 * @method static ProductAssociationTypeInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductAssociationTypeInterface[]|Proxy[] all()
 * @method static ProductAssociationTypeInterface[]|Proxy[] findBy(array $attributes)
 * @method static ProductAssociationTypeInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ProductAssociationTypeInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ProductAssociationTypeInterface|Proxy create(array|callable $attributes = [])
 */
interface ProductAssociationTypeFactoryInterface extends WithCodeInterface, WithNameInterface
{
}
