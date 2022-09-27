<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\PromotionActionDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\PromotionActionTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\PromotionActionUpdaterInterface;
use Sylius\Component\Promotion\Model\PromotionAction;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
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
class PromotionActionFactory extends ModelFactory implements PromotionActionFactoryInterface, FactoryWithModelClassAwareInterface
{
    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface                      $promotionActionFactory,
        private PromotionActionDefaultValuesInterface $factoryDefaultValues,
        private PromotionActionTransformerInterface   $factoryTransformer,
        private PromotionActionUpdaterInterface       $factoryUpdater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
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
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
    }

    protected function update(PromotionActionInterface $promotionAction, array $attributes): void
    {
        $this->factoryUpdater->update($promotionAction, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): PromotionActionInterface {
                /** @var PromotionActionInterface $promotionAction */
                $promotionAction = $this->promotionActionFactory->createNew();

                $this->update($promotionAction, $attributes);

                return $promotionAction;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? PromotionAction::class;
    }
}
