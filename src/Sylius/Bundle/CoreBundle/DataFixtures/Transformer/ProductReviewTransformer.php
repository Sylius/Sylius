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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;

final class ProductReviewTransformer implements ProductReviewTransformerInterface
{
    use TransformProductAttributeTrait;
    use TransformCustomerAttributeTrait;

    public function __construct(
        private CustomerFactoryInterface $customerFactory,
        private ProductFactoryInterface  $productFactory,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformCustomerAttribute($attributes, 'author');

        return $this->transformProductAttribute($attributes);
    }
}
