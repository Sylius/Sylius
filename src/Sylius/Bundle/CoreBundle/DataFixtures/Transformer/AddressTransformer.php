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

final class AddressTransformer implements AddressTransformerInterface
{
    use TransformCustomerAttributeTrait;

    public function __construct(private CustomerFactoryInterface $customerFactory)
    {
    }

    public function transform(array $attributes): array
    {
        return $this->transformCustomerAttribute($attributes);
    }
}
