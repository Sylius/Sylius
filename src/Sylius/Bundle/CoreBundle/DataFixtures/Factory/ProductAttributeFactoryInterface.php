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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCodeInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithNameInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ProductAttributeInterface>
 *
 * @method static ProductAttributeInterface|Proxy createOne(array $attributes = [])
 * @method static ProductAttributeInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductAttributeInterface|Proxy find(object|array|mixed $criteria)
 * @method static ProductAttributeInterface|Proxy findOrCreate(array $attributes)
 * @method static ProductAttributeInterface|Proxy first(string $sortedField = 'id')
 * @method static ProductAttributeInterface|Proxy last(string $sortedField = 'id')
 * @method static ProductAttributeInterface|Proxy random(array $attributes = [])
 * @method static ProductAttributeInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductAttributeInterface[]|Proxy[] all()
 * @method static ProductAttributeInterface[]|Proxy[] findBy(array $attributes)
 * @method static ProductAttributeInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ProductAttributeInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ProductAttributeInterface|Proxy create(array|callable $attributes = [])
 */
interface ProductAttributeFactoryInterface extends WithCodeInterface, WithNameInterface
{
    public function withType(string $type): self;

    public function translatable(): self;

    public function untranslatable(): self;

    public function withConfiguration(array $configuration): self;
}
