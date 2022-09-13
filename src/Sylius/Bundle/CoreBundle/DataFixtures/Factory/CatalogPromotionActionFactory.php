<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues\CatalogPromotionActionFactoryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer\CatalogPromotionActionFactoryTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater\CatalogPromotionActionFactoryUpdaterInterface;
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
class CatalogPromotionActionFactory extends ModelFactory implements CatalogPromotionActionFactoryInterface, FactoryWithModelClassAwareInterface
{
    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $catalogPromotionActionFactory,
        private CatalogPromotionActionFactoryDefaultValuesInterface $factoryDefaultValues,
        private CatalogPromotionActionFactoryTransformerInterface $factoryTransformer,
        private CatalogPromotionActionFactoryUpdaterInterface $factoryUpdater,
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

    protected function update(CatalogPromotionActionInterface $catalogPromotionAction, array $attributes): void
    {
        $this->factoryUpdater->update($catalogPromotionAction, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): CatalogPromotionActionInterface {
                /** @var CatalogPromotionActionInterface $catalogPromotionAction */
                $catalogPromotionAction = $this->catalogPromotionActionFactory->createNew();

                $this->update($catalogPromotionAction, $attributes);

                return $catalogPromotionAction;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? CatalogPromotionAction::class;
    }
}
