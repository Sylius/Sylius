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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopUserFactoryInterface;

final class ProductReviewTransformer implements ProductReviewTransformerInterface
{
    use TransformProductAttributeTrait;

    public function __construct(
        private ShopUserFactoryInterface $shopUserFactory,
        private ProductFactoryInterface $productFactory,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformAuthorAttribute($attributes);

        return $this->transformProductOptionsAttribute($attributes);
    }

    private function transformAuthorAttribute(array $attributes): array
    {
        if (\is_string($attributes['author'])) {
            $attributes['author'] = $this->shopUserFactory::findOrCreate(['email' => $attributes['author']])->getCustomer();
        }

        return $attributes;
    }
}
