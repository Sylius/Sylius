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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerGroupFactoryInterface;

final class CustomerTransformer implements CustomerTransformerInterface
{
    public function __construct(private CustomerGroupFactoryInterface $customerGroupFactory)
    {
    }

    public function transform(array $attributes): array
    {
        if (\is_string($attributes['customer_group'])) {
            $attributes['customer_group'] = $this->customerGroupFactory::randomOrCreate(['code' => $attributes['customer_group']]);
        }

        return $attributes;
    }
}
