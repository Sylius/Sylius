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

namespace Sylius\Component\Core\TokenAssigner;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;

final class UniqueIdBasedOrderTokenAssigner implements OrderTokenAssignerInterface
{
    public function __construct(private RandomnessGeneratorInterface $generator)
    {
    }

    public function assignTokenValue(OrderInterface $order): void
    {
        $order->setTokenValue($this->generator->generateUriSafeString(10));
    }

    public function assignTokenValueIfNotSet(OrderInterface $order): void
    {
        if (null === $order->getTokenValue()) {
            $this->assignTokenValue($order);
        }
    }
}
