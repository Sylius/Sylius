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

namespace Sylius\Bundle\CoreBundle\OrderPay\Provider;

use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @experimental */
interface UrlProviderInterface
{
    public function getUrl(
        PaymentRequestInterface $paymentRequest,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string;
}
