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

use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<PromotionRuleInterface>
 *
 * @method static PromotionRuleInterface|Proxy createOne(array $attributes = [])
 * @method static PromotionRuleInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static PromotionRuleInterface|Proxy find(object|array|mixed $criteria)
 * @method static PromotionRuleInterface|Proxy findOrCreate(array $attributes)
 * @method static PromotionRuleInterface|Proxy first(string $sortedField = 'id')
 * @method static PromotionRuleInterface|Proxy last(string $sortedField = 'id')
 * @method static PromotionRuleInterface|Proxy random(array $attributes = [])
 * @method static PromotionRuleInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static PromotionRuleInterface[]|Proxy[] all()
 * @method static PromotionRuleInterface[]|Proxy[] findBy(array $attributes)
 * @method static PromotionRuleInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static PromotionRuleInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method PromotionRuleInterface|Proxy create(array|callable $attributes = [])
 */
interface PromotionRuleFactoryInterface
{
    public function withType(string $type): self;

    public function withConfiguration(array $configuration): self;
}
