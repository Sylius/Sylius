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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopUserFactoryInterface;

final class AddressFactoryTransformer implements AddressFactoryTransformerInterface
{
    public function __construct(private ShopUserFactoryInterface $shopUserFactory)
    {
    }

    public function transform(array $attributes): array
    {
        if (\is_string($attributes['customer'])) {
            $attributes['customer'] = $this->shopUserFactory::findOrCreate(['email' => $attributes['customer']])->getCustomer();
        }

        return $attributes;
    }
}
