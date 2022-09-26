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

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ProductAssociationInterface>
 *
 * @method static ProductAssociationInterface|Proxy createOne(array $attributes = [])
 * @method static ProductAssociationInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductAssociationInterface|Proxy find(object|array|mixed $criteria)
 * @method static ProductAssociationInterface|Proxy findOrCreate(array $attributes)
 * @method static ProductAssociationInterface|Proxy first(string $sortedField = 'id')
 * @method static ProductAssociationInterface|Proxy last(string $sortedField = 'id')
 * @method static ProductAssociationInterface|Proxy random(array $attributes = [])
 * @method static ProductAssociationInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductAssociationInterface[]|Proxy[] all()
 * @method static ProductAssociationInterface[]|Proxy[] findBy(array $attributes)
 * @method static ProductAssociationInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ProductAssociationInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ProductAssociationInterface|Proxy create(array|callable $attributes = [])
 */
interface ProductAssociationFactoryInterface
{
    public function withType(Proxy|ProductAssociationTypeInterface|string $owner): self;

    public function withOwner(Proxy|ProductInterface|string $owner): self;
}
