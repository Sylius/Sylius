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

namespace Sylius\Bundle\PaymentBundle\Checker;

use Sylius\Component\Payment\Checker\PaymentMethodGatewayFactoryCheckerInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;

final class PaymentMethodGatewayFactoryChecker implements PaymentMethodGatewayFactoryCheckerInterface
{
    /** @param PaymentMethodRepositoryInterface<PaymentMethodInterface> $paymentMethodRepository */
    public function __construct(
        private readonly PaymentMethodRepositoryInterface $paymentMethodRepository,
    ) {
    }

    public function hasPaymentMethodWithGatewayFactory(string $factoryName): bool
    {
        return $this->paymentMethodRepository->countByGatewayFactoryName($factoryName) > 0;
    }
}
