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

use Sylius\Component\Core\Formatter\StringInflector;

final class PaymentMethodFactoryTransformer implements PaymentMethodFactoryTransformerInterface
{
    public function transform(array $attributes): array
    {
        if (null === $attributes['code']) {
            $attributes['code'] = StringInflector::nameToCode($attributes['name']);
        }

        return $attributes;
    }
}
