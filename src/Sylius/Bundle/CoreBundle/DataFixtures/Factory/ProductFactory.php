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

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\ProductDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\ProductTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\ProductUpdaterInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ProductInterface>
 *
 * @method static ProductInterface|Proxy createOne(array $attributes = [])
 * @method static ProductInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ProductInterface|Proxy find(object|array|mixed $criteria)
 * @method static ProductInterface|Proxy findOrCreate(array $attributes)
 * @method static ProductInterface|Proxy first(string $sortedField = 'id')
 * @method static ProductInterface|Proxy last(string $sortedField = 'id')
 * @method static ProductInterface|Proxy random(array $attributes = [])
 * @method static ProductInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductInterface[]|Proxy[] all()
 * @method static ProductInterface[]|Proxy[] findBy(array $attributes)
 * @method static ProductInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ProductInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method ProductInterface|Proxy create(array|callable $attributes = [])
 */
class ProductFactory extends ModelFactory implements ProductFactoryInterface
{
    use WithCodeTrait;
    use WithNameTrait;
    use WithDescriptionTrait;
    use ToggableTrait;
    use WithTaxCategoryTrait;
    use WithChannelsTrait;
    use WithTaxaTrait;

    public function __construct(
        private FactoryInterface $productFactory,
        private ProductDefaultValuesInterface $factoryDefaultValues,
        private ProductTransformerInterface  $factoryTransformer,
        private ProductUpdaterInterface $factoryUpdater,
    ) {
        parent::__construct();
    }

    public function tracked(): self
    {
        return $this->addState(['tracked' => true]);
    }

    public function untracked(): self
    {
        return $this->addState(['tracked' => false]);
    }

    public function withSlug(string $slug): self
    {
        return $this->addState(['slug' => $slug]);
    }

    public function withShortDescription(string $shortDescription): self
    {
        return $this->addState(['short_description' => $shortDescription]);
    }

    public function withVariantSelectionMethod(string $variantSelectionMethod): self
    {
        return $this->addState(['variant_selection_method' => $variantSelectionMethod]);
    }

    public function withShippingRequired(): self
    {
        return $this->addState(['shipping_required' => true]);
    }

    public function withShippingNotRequired(): self
    {
        return $this->addState(['shipping_required' => false]);
    }

    public function withMainTaxon(Proxy|TaxonInterface|string $mainTaxon): self
    {
        return $this->addState(['main_taxon' => $mainTaxon]);
    }

    public function withProductAttributes(array $productAttributes): self
    {
        return $this->addState(['product_attributes' => $productAttributes]);
    }

    public function withProductOptions(array $productOptions): self
    {
        return $this->addState(['product_options' => $productOptions]);
    }

    public function withImages(array $images): self
    {
        return $this->addState(['images' => $images]);
    }

    protected function getDefaults(): array
    {
        return $this->factoryDefaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->factoryTransformer->transform($attributes);
    }

    protected function update(ProductInterface $product, array $attributes): void
    {
        $this->factoryUpdater->update($product, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(): ProductInterface {
                /** @var ProductInterface $product */
                $product = $this->productFactory->createNew();

                return $product;
            })
            ->afterInstantiate(function (ProductInterface $product, array $attributes): void {
                $this->update($product, $attributes);
            })
        ;
    }

    protected static function getClass(): string
    {
        return Product::class;
    }
}
