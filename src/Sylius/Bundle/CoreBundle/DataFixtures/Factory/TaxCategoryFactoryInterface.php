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

use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<TaxCategoryInterface>
 *
 * @method static TaxCategoryInterface|Proxy createOne(array $attributes = [])
 * @method static TaxCategoryInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static TaxCategoryInterface|Proxy find(object|array|mixed $criteria)
 * @method static TaxCategoryInterface|Proxy findOrCreate(array $attributes)
 * @method static TaxCategoryInterface|Proxy first(string $sortedField = 'id')
 * @method static TaxCategoryInterface|Proxy last(string $sortedField = 'id')
 * @method static TaxCategoryInterface|Proxy random(array $attributes = [])
 * @method static TaxCategoryInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static TaxCategoryInterface[]|Proxy[] all()
 * @method static TaxCategoryInterface[]|Proxy[] findBy(array $attributes)
 * @method static TaxCategoryInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static TaxCategoryInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method TaxCategoryInterface|Proxy create(array|callable $attributes = [])
 */
interface TaxCategoryFactoryInterface extends WithCodeInterface, WithNameInterface
{
    public function withDescription(string $description): self;
}
