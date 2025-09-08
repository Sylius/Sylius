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

namespace Sylius\Bundle\AdminBundle\PendingAction\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Webmozart\Assert\Assert;

final readonly class CountPendingPaymentsProvider implements PendingActionCountProviderInterface
{
    public function __construct(private PaymentRepositoryInterface $paymentRepository)
    {
    }

    public function count(?ChannelInterface $channel = null): int
    {
        Assert::notNull($channel);

        return $this->paymentRepository->countNewByChannel($channel);
    }
}
