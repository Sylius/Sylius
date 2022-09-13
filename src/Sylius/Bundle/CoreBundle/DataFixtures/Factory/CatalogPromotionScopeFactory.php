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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues\CatalogPromotionScopeFactoryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer\CatalogPromotionScopeFactoryTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater\CatalogPromotionScopeFactoryUpdaterInterface;
use Sylius\Component\Core\Model\CatalogPromotionScope;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
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
class CatalogPromotionScopeFactory extends ModelFactory implements CatalogPromotionScopeFactoryInterface, FactoryWithModelClassAwareInterface
{
    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $catalogPromotionScopeFactory,
        private CatalogPromotionScopeFactoryDefaultValuesInterface $factoryDefaultValues,
        private CatalogPromotionScopeFactoryTransformerInterface $factoryTransformer,
        private CatalogPromotionScopeFactoryUpdaterInterface $factoryUpdater,
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

    protected function update(CatalogPromotionScopeInterface $catalogPromotionScope, array $attributes): void
    {
        $this->factoryUpdater->update($catalogPromotionScope, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): CatalogPromotionScopeInterface {
                /** @var CatalogPromotionScopeInterface $catalogPromotionScope */
                $catalogPromotionScope = $this->catalogPromotionScopeFactory->createNew();

                $this->update($catalogPromotionScope, $attributes);

                return $catalogPromotionScope;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? CatalogPromotionScope::class;
    }
}
