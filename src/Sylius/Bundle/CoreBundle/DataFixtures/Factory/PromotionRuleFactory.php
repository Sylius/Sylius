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

use Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRule;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
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
class PromotionRuleFactory extends ModelFactory implements PromotionRuleFactoryInterface
{
    public function __construct(private FactoryInterface $promotionRuleFactory)
    {
        parent::__construct();
    }

    public function withType(string $type): self
    {
        return $this->addState(['type' => $type]);
    }

    public function withConfiguration(array $configuration): self
    {
        return $this->addState(['configuration' => $configuration]);
    }

    protected function getDefaults(): array
    {
        return [
            'type' => CartQuantityRuleChecker::TYPE,
            'configuration' => ['count' => self::faker()->randomNumber(1)],
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                $configuration = &$attributes['configuration'];

                foreach ($configuration as $channelCode => $channelConfiguration) {
                    if (isset($channelConfiguration['amount'])) {
                        $configuration[$channelCode]['amount'] = (int) ($channelConfiguration['amount'] * 100);
                    }
                }

                return $attributes;
            })
            ->instantiateWith(function(array $attributes): PromotionRuleInterface {
                /** @var PromotionRuleInterface $promotionRule */
                $promotionRule = $this->promotionRuleFactory->createNew();

                $promotionRule->setType($attributes['type']);
                $promotionRule->setConfiguration($attributes['configuration']);

                return $promotionRule;
            })
        ;
    }

    protected static function getClass(): string
    {
        return PromotionRule::class;
    }
}
