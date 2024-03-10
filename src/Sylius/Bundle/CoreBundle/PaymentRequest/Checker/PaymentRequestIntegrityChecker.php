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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Checker;

use Sylius\Bundle\CoreBundle\PaymentRequest\Command\PaymentRequestHashAwareInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Exception\NullGatewayConfigException;
use Sylius\Component\Payment\Exception\PaymentRequestNotFoundException;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class PaymentRequestIntegrityChecker implements PaymentRequestIntegrityCheckerInterface
{
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
    }

    public function check(PaymentRequestHashAwareInterface $command): PaymentRequestInterface
    {
        $hash = $command->getHash();

        $paymentRequest = $this->paymentRequestRepository->findOneByHash($hash);
        if (null === $paymentRequest) {
            throw new PaymentRequestNotFoundException();
        }

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if (null === $gatewayConfig) {
            throw new NullGatewayConfigException();
        }

        return $paymentRequest;
    }
}
