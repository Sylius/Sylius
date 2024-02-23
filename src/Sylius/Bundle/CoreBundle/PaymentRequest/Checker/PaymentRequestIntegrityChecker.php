<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Checker;

use Sylius\Bundle\CoreBundle\PaymentRequest\Command\PaymentRequestHashAwareInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Webmozart\Assert\Assert;

final class PaymentRequestIntegrityChecker implements PaymentRequestIntegrityCheckerInterface
{
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
    }

    public function check(PaymentRequestHashAwareInterface $command): PaymentRequestInterface
    {
        $hash = $command->getHash();
        Assert::notNull($hash, 'Payment request hash cannot be null.');

        $paymentRequest = $this->paymentRequestRepository->findOneByHash($hash);
        Assert::notNull($paymentRequest, sprintf('Payment request (hash "%s") not found.', $hash));

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();
        Assert::notNull($paymentMethod, 'Payment cannot be null.');

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig, 'Payment method cannot be null.');

        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment, 'Payment cannot be null.');

        return $paymentRequest;
    }
}
