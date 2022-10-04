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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\ProductAttributeDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\TranslatableTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCodeTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithNameTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\ProductAttributeTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\ProductAttributeUpdaterInterface;
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Product\Model\ProductAttribute;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ProductAttributeInterface>
 *
 * @method static ProductAttributeInterface|Proxy createOne(array $attributes = [])
 * @method static ProductAttributeInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductAttributeInterface|Proxy find(object|array|mixed $criteria)
 * @method static ProductAttributeInterface|Proxy findOrCreate(array $attributes)
 * @method static ProductAttributeInterface|Proxy first(string $sortedField = 'id')
 * @method static ProductAttributeInterface|Proxy last(string $sortedField = 'id')
 * @method static ProductAttributeInterface|Proxy random(array $attributes = [])
 * @method static ProductAttributeInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductAttributeInterface[]|Proxy[] all()
 * @method static ProductAttributeInterface[]|Proxy[] findBy(array $attributes)
 * @method static ProductAttributeInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ProductAttributeInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ProductAttributeInterface|Proxy create(array|callable $attributes = [])
 */
class ProductAttributeFactory extends ModelFactory implements ProductAttributeFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;
    use WithNameTrait;
    use TranslatableTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private AttributeFactoryInterface $productAttributeFactory,
        private ProductAttributeDefaultValuesInterface $defaultValues,
        private ProductAttributeTransformerInterface $transformer,
        private ProductAttributeUpdaterInterface $updater,
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
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(ProductAttributeInterface $productAttribute, array $attributes): void
    {
        $this->updater->update($productAttribute, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): ProductAttributeInterface {
                /** @var ProductAttributeInterface $productAttribute */
                $productAttribute = $this->productAttributeFactory->createTyped($attributes['type']);

                $this->update($productAttribute, $attributes);

                return $productAttribute;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? ProductAttribute::class;
    }
}
