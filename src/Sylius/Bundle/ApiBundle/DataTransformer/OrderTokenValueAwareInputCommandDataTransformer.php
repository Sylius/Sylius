<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderTokenValueAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        /** @var OrderInterface $cart */
        $cart = $context['object_to_populate'];

        $object->setOrderTokenValue($cart->getTokenValue());

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof OrderTokenValueAwareInterface;
    }
}
