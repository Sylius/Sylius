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

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
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
interface ProductFactoryInterface extends WithCodeInterface, WithNameInterface, WithDescriptionInterface, ToggableInterface, WithTaxCategoryInterface, WithChannelsInterface, WithTaxaInterface
{
    public function tracked(): self;

    public function untracked(): self;

    public function withSlug(string $slug): self;

    public function withShortDescription(string $shortDescription): self;

    public function withVariantSelectionMethod(string $variantSelectionMethod): self;

    public function withShippingRequired(): self;

    public function withShippingNotRequired(): self;

    public function withMainTaxon(Proxy|TaxonInterface|string $mainTaxon): self;

    public function withProductAttributes(array $productAttributes): self;

    public function withProductOptions(array $productOptions): self;

    public function withImages(array $images): self;
}
