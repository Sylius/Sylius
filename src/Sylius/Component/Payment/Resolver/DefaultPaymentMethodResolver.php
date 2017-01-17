<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Resolver;

use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class DefaultPaymentMethodResolver implements DefaultPaymentMethodResolverInterface
{
    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     */
    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultPaymentMethod(PaymentInterface $payment)
    {
        $paymentMethods = $this->paymentMethodRepository->findBy(['enabled' => true]);
        if (empty($paymentMethods)) {
            throw new UnresolvedDefaultPaymentMethodException();
        }

        return $paymentMethods[0];
    }
}
