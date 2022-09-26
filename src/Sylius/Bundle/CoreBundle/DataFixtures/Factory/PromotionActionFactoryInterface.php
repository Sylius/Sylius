<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<PromotionActionInterface>
 *
 * @method static PromotionActionInterface|Proxy createOne(array $attributes = [])
 * @method static PromotionActionInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static PromotionActionInterface|Proxy find(object|array|mixed $criteria)
 * @method static PromotionActionInterface|Proxy findOrCreate(array $attributes)
 * @method static PromotionActionInterface|Proxy first(string $sortedField = 'id')
 * @method static PromotionActionInterface|Proxy last(string $sortedField = 'id')
 * @method static PromotionActionInterface|Proxy random(array $attributes = [])
 * @method static PromotionActionInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static PromotionActionInterface[]|Proxy[] all()
 * @method static PromotionActionInterface[]|Proxy[] findBy(array $attributes)
 * @method static PromotionActionInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static PromotionActionInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method PromotionActionInterface|Proxy create(array|callable $attributes = [])
 */
interface PromotionActionFactoryInterface
{
    public function withType(string $type): self;

    public function withConfiguration(array $configuration): self;
}
