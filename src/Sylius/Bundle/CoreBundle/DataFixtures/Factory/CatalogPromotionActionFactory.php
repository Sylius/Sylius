<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\Calculator\PercentageDiscountPriceCalculator;
use Sylius\Component\Promotion\Model\CatalogPromotionAction;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
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
class CatalogPromotionActionFactory extends ModelFactory
{
    public function __construct(private FactoryInterface $catalogPromotionActionFactory)
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
            'type' => PercentageDiscountPriceCalculator::TYPE,
            'configuration' => [],
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                if ($attributes['type'] !== FixedDiscountPriceCalculator::TYPE) {
                    return $attributes;
                }

                $configuration = &$attributes['configuration'];

                foreach ($configuration as $channelCode => $channelConfiguration) {
                    if (isset($channelConfiguration['amount'])) {
                        $configuration[$channelCode]['amount'] = (int) ($channelConfiguration['amount'] * 100);
                    }
                }

                return $attributes;
            })
            ->instantiateWith(function(array $attributes): CatalogPromotionActionInterface {
                /** @var CatalogPromotionActionInterface $catalogPromotionAction */
                $catalogPromotionAction = $this->catalogPromotionActionFactory->createNew();

                $catalogPromotionAction->setType($attributes['type']);
                $catalogPromotionAction->setConfiguration($attributes['configuration']);

                return $catalogPromotionAction;
            })
        ;
    }

    protected static function getClass(): string
    {
        return CatalogPromotionAction::class;
    }
}
