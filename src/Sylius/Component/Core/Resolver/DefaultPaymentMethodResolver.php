<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Resolver;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Webmozart\Assert\Assert;

class DefaultPaymentMethodResolver implements DefaultPaymentMethodResolverInterface
{
    /** @var PaymentMethodRepositoryInterface */
    protected $paymentMethodRepository;

    /** @var PaymentMethodsResolverInterface|null */
    private $paymentMethodsResolver;

    public function __construct(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ?PaymentMethodsResolverInterface $paymentMethodsResolver = null
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;

        if (null === $paymentMethodsResolver) {
            @trigger_error(
                sprintf(
                    'Not passing an $paymentMethodsResolver to "%s" constructor is deprecated since Sylius 1.8 and will be impossible in Sylius 2.0.',
                    self::class
                ),
                \E_USER_DEPRECATED
            );
        }

        $this->paymentMethodsResolver = $paymentMethodsResolver;
    }

    /**
     * @param BasePaymentInterface|PaymentInterface $payment
     *
     * @throws UnresolvedDefaultPaymentMethodException
     */
    public function getDefaultPaymentMethod(BasePaymentInterface $payment): PaymentMethodInterface
    {
        Assert::isInstanceOf($payment, PaymentInterface::class);

        $channel = $payment->getOrder()->getChannel();

        if (null !== $this->paymentMethodsResolver) {
            $paymentMethods = $this->paymentMethodsResolver->getSupportedMethods($payment);
        } else {
            $paymentMethods = $this->paymentMethodRepository->findEnabledForChannel($channel);
        }

        if (empty($paymentMethods)) {
            throw new UnresolvedDefaultPaymentMethodException();
        }

        return $paymentMethods[0];
    }
}
