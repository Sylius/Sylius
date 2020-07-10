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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class RemoveItemFromCartDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        /** @var RemoveItemFromCart|mixed $object */
        Assert::isInstanceOf($object, RemoveItemFromCart::class);

        /** @var OrderInterface $cart */
        $cart = $context['object_to_populate'];

        $object->tokenValue = $cart->getTokenValue();

        return $object;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return RemoveItemFromCart::class === $context['input']['class'];
    }
}
