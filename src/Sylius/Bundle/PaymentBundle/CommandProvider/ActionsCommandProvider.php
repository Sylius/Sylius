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

namespace Sylius\Bundle\PaymentBundle\CommandProvider;

use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

/** @experimental */
final class ActionsCommandProvider extends AbstractServiceCommandProvider
{
    /** @param ServiceProviderInterface<PaymentRequestCommandProviderInterface> $locator */
    public function __construct(protected ServiceProviderInterface $locator)
    {
    }

    protected function getCommandProviderIndex(PaymentRequestInterface $paymentRequest): string
    {
        return $paymentRequest->getAction();
    }
}
