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
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class OrderTokenValueAwareInputDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        /** @var OrderTokenValueAwareInterface|mixed $object */
        Assert::isInstanceOf($object, OrderTokenValueAwareInterface::class);

        /** @var OrderInterface $cart */
        $cart = $context['object_to_populate'];

        $object->setOrderTokenValue($cart->getTokenValue());

        return $object;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return is_a($context['input']['class'], OrderTokenValueAwareInterface::class, true);
    }
}
