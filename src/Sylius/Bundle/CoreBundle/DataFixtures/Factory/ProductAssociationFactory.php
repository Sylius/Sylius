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
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\ProductAssociationTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\ProductAssociationUpdaterInterface;
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
        private ProductAssociationDefaultValuesInterface $defaultValues,
        private ProductAssociationTransformerInterface $transformer,
        private ProductAssociationUpdaterInterface $updater,
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

    public function withAssociatedProducts(array $associatedProducts): self
    {
        return $this->addState(['associated_products' => $associatedProducts]);
    }

    protected function getDefaults(): array
    {
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(ProductAssociationInterface $productAssociation, array $attributes): void
    {
        $this->updater->update($productAssociation, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function(array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): ProductAssociationInterface {
                /** @var ProductAssociationInterface $productAssociation */
                $productAssociation = $this->ProductAssociationFactory->createNew();

                $this->update($productAssociation, $attributes);

                return $productAssociation;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? ProductAssociation::class;
    }
}
