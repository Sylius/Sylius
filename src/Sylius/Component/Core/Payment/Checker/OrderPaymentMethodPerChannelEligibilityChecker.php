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

namespace Sylius\Component\Core\Payment\Checker;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class OrderPaymentMethodPerChannelEligibilityChecker implements OrderPaymentMethodPerChannelEligibilityCheckerInterface
{
    public function __construct(private readonly ChannelContextInterface $channelContext)
    {
    }

    public function isEligible(?PaymentMethodInterface $paymentMethod): bool
    {
        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException) {
            return false;
        }

        return $paymentMethod->hasChannel($channel);
    }
}
