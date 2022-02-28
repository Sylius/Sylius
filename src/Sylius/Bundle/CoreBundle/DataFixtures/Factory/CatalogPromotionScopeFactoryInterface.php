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

use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CatalogPromotionScopeInterface>
 *
 * @method static CatalogPromotionScopeInterface|Proxy createOne(array $attributes = [])
 * @method static CatalogPromotionScopeInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CatalogPromotionScopeInterface|Proxy find(object|array|mixed $criteria)
 * @method static CatalogPromotionScopeInterface|Proxy findOrCreate(array $attributes)
 * @method static CatalogPromotionScopeInterface|Proxy first(string $sortedField = 'id')
 * @method static CatalogPromotionScopeInterface|Proxy last(string $sortedField = 'id')
 * @method static CatalogPromotionScopeInterface|Proxy random(array $attributes = [])
 * @method static CatalogPromotionScopeInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CatalogPromotionScopeInterface[]|Proxy[] all()
 * @method static CatalogPromotionScopeInterface[]|Proxy[] findBy(array $attributes)
 * @method static CatalogPromotionScopeInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CatalogPromotionScopeInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CatalogPromotionScopeInterface|Proxy create(array|callable $attributes = [])
 */
interface CatalogPromotionScopeFactoryInterface
{
    public function withType(string $type): self;

    public function withConfiguration(array $configuration): self;
}
