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

namespace Sylius\Bundle\PaymentBundle\Provider;

use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

/** @experimental */
final class ActionsHttpResponseProvider extends AbstractServiceProvider
{
    /** @param ServiceProviderInterface<HttpResponseProviderInterface> $locator */
    public function __construct(protected ServiceProviderInterface $locator)
    {
    }

    protected function getHttpResponseProviderIndex(PaymentRequestInterface $paymentRequest): string
    {
        return $paymentRequest->getAction();
    }
}
