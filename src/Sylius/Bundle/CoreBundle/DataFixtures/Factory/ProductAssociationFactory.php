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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\ProductAssociationDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer\ProductAssociationTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater\ProductAssociationFactoryUpdaterInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAssociation;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ProductAssociationInterface>
 *
 * @method static ProductAssociationInterface|Proxy createOne(array $attributes = [])
 * @method static ProductAssociationInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductAssociationInterface|Proxy find(object|array|mixed $criteria)
 * @method static ProductAssociationInterface|Proxy findOrCreate(array $attributes)
 * @method static ProductAssociationInterface|Proxy first(string $sortedField = 'id')
 * @method static ProductAssociationInterface|Proxy last(string $sortedField = 'id')
 * @method static ProductAssociationInterface|Proxy random(array $attributes = [])
 * @method static ProductAssociationInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductAssociationInterface[]|Proxy[] all()
 * @method static ProductAssociationInterface[]|Proxy[] findBy(array $attributes)
 * @method static ProductAssociationInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ProductAssociationInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ProductAssociationInterface|Proxy create(array|callable $attributes = [])
 */
class ProductAssociationFactory extends ModelFactory implements ProductAssociationFactoryInterface, FactoryWithModelClassAwareInterface
{
    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $ProductAssociationFactory,
        private ProductAssociationDefaultValuesInterface $factoryDefaultValues,
        private ProductAssociationTransformerInterface $factoryTransformer,
        private ProductAssociationFactoryUpdaterInterface $factoryUpdater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    public function withType(Proxy|ProductAssociationTypeInterface|string $owner): self
    {
        return $this->addState(['type' => $owner]);
    }

    public function withOwner(Proxy|ProductInterface|string $owner): self
    {
        return $this->addState(['owner' => $owner]);
    }

    protected function getDefaults(): array
    {
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
    }

    protected function update(ProductAssociationInterface $ProductAssociation, array $attributes): void
    {
        $this->factoryUpdater->update($ProductAssociation, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): ProductAssociationInterface {
                /** @var ProductAssociationInterface $ProductAssociation */
                $ProductAssociation = $this->ProductAssociationFactory->createNew();

                $this->update($ProductAssociation, $attributes);

                return $ProductAssociation;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? ProductAssociation::class;
    }
}
