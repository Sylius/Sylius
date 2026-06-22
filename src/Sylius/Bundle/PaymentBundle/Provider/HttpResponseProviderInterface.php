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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @experimental */
interface HttpResponseProviderInterface
{
    public function supports(
        Request $request,
        PaymentRequestInterface $paymentRequest,
    ): bool;

    public function getResponse(
        Request $request,
        PaymentRequestInterface $paymentRequest,
    ): Response;
}
