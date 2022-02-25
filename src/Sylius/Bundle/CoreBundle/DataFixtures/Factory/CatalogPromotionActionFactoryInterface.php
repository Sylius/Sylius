<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CatalogPromotionActionInterface>
 *
 * @method static CatalogPromotionActionInterface|Proxy createOne(array $attributes = [])
 * @method static CatalogPromotionActionInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CatalogPromotionActionInterface|Proxy find(object|array|mixed $criteria)
 * @method static CatalogPromotionActionInterface|Proxy findOrCreate(array $attributes)
 * @method static CatalogPromotionActionInterface|Proxy first(string $sortedField = 'id')
 * @method static CatalogPromotionActionInterface|Proxy last(string $sortedField = 'id')
 * @method static CatalogPromotionActionInterface|Proxy random(array $attributes = [])
 * @method static CatalogPromotionActionInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CatalogPromotionActionInterface[]|Proxy[] all()
 * @method static CatalogPromotionActionInterface[]|Proxy[] findBy(array $attributes)
 * @method static CatalogPromotionActionInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CatalogPromotionActionInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CatalogPromotionActionInterface|Proxy create(array|callable $attributes = [])
 */
interface CatalogPromotionActionFactoryInterface
{
    public function withType(string $type): self;
}
