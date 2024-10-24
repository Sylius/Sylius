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

namespace Sylius\Component\Core\Resolver;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Webmozart\Assert\Assert;

final class ChannelBasedPaymentMethodsResolver implements PaymentMethodsResolverInterface
{
    public function __construct(private PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
    }

    public function getSupportedMethods(BasePaymentInterface $subject): array
    {
        /** @var PaymentInterface $subject */
        Assert::isInstanceOf($subject, PaymentInterface::class);
        Assert::true($this->supports($subject), 'This payment method is not support by resolver');

        return $this->paymentMethodRepository->findEnabledForChannel($subject->getOrder()->getChannel());
    }

    public function supports(BasePaymentInterface $subject): bool
    {
        return $subject instanceof PaymentInterface &&
            null !== $subject->getOrder() &&
            null !== $subject->getOrder()->getChannel()
        ;
    }
}
