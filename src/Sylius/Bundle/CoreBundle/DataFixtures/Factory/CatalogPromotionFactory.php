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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues\CatalogPromotionFactoryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer\CatalogPromotionFactoryTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater\CatalogPromotionFactoryUpdaterInterface;
use Sylius\Component\Core\Model\CatalogPromotion;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CatalogPromotionInterface>
 *
 * @method static CatalogPromotionInterface|Proxy createOne(array $attributes = [])
 * @method static CatalogPromotionInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CatalogPromotionInterface|Proxy find(object|array|mixed $criteria)
 * @method static CatalogPromotionInterface|Proxy findOrCreate(array $attributes)
 * @method static CatalogPromotionInterface|Proxy first(string $sortedField = 'id')
 * @method static CatalogPromotionInterface|Proxy last(string $sortedField = 'id')
 * @method static CatalogPromotionInterface|Proxy random(array $attributes = [])
 * @method static CatalogPromotionInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CatalogPromotionInterface[]|Proxy[] all()
 * @method static CatalogPromotionInterface[]|Proxy[] findBy(array $attributes)
 * @method static CatalogPromotionInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CatalogPromotionInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CatalogPromotionInterface|Proxy create(array|callable $attributes = [])
 */
class CatalogPromotionFactory extends ModelFactory implements CatalogPromotionFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;
    use WithNameTrait;
    use ToggableTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $catalogPromotionFactory,
        private CatalogPromotionFactoryDefaultValuesInterface $factoryDefaultValues,
        private CatalogPromotionFactoryTransformerInterface $factoryTransformer,
        private CatalogPromotionFactoryUpdaterInterface $factoryUpdater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withLabel(string $label): self
    {
        return $this->addState(['label' => $label]);
    }

    public function withDescription(string $description): self
    {
        return $this->addState(['description' => $description]);
    }

    public function withChannels(array $channels): self
    {
        return $this->addState(['channels' => $channels]);
    }

    public function withScopes(array $scopes): self
    {
        return $this->addState(['scopes' => $scopes]);
    }

    public function withActions(array $actions): self
    {
        return $this->addState(['actions' => $actions]);
    }

    public function withPriority(int $priority): self
    {
        return $this->addState(['priority' => $priority]);
    }

    public function exclusive(): self
    {
        return $this->addState(['exclusive' => true]);
    }

    public function notExclusive(): self
    {
        return $this->addState(['exclusive' => false]);
    }

    public function withStartDate(\DateTimeInterface|string $startDate): self
    {
        return $this->addState(['start_date' => $startDate]);
    }

    public function withEndDate(\DateTimeInterface|string $endDate): self
    {
        return $this->addState(['end_date' => $endDate]);
    }

    protected function getDefaults(): array
    {
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
    }

    protected function update(CatalogPromotionInterface $catalogPromotion, array $attributes): void
    {
        $this->factoryUpdater->update($catalogPromotion, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): CatalogPromotionInterface {
                /** @var CatalogPromotionInterface $catalogPromotion */
                $catalogPromotion = $this->catalogPromotionFactory->createNew();

                $this->update($catalogPromotion, $attributes);

                return $catalogPromotion;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? CatalogPromotion::class;
    }
}
