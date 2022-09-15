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

use Sylius\Component\Product\Model\ProductOptionInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ProductOptionInterface>
 *
 * @method static ProductOptionInterface|Proxy createOne(array $attributes = [])
 * @method static ProductOptionInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductOptionInterface|Proxy find(object|array|mixed $criteria)
 * @method static ProductOptionInterface|Proxy findOrCreate(array $attributes)
 * @method static ProductOptionInterface|Proxy first(string $sortedField = 'id')
 * @method static ProductOptionInterface|Proxy last(string $sortedField = 'id')
 * @method static ProductOptionInterface|Proxy random(array $attributes = [])
 * @method static ProductOptionInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductOptionInterface[]|Proxy[] all()
 * @method static ProductOptionInterface[]|Proxy[] findBy(array $attributes)
 * @method static ProductOptionInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ProductOptionInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ProductOptionInterface|Proxy create(array|callable $attributes = [])
 */
interface ProductOptionFactoryInterface extends WithCodeInterface
{
    public function withName(string $name): self;

    public function withValues(array $values): self;
}
