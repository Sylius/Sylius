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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Webmozart\Assert\Assert;

final class PaymentMethodContext implements Context
{
    public function __construct(private PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
    }

    /**
     * @Transform /^"([^"]+)" payment(s)?$/
     * @Transform :paymentMethod
     */
    public function getPaymentMethodByName($paymentMethodName)
    {
        $paymentMethods = $this->paymentMethodRepository->findByName($paymentMethodName, 'en_US');

        Assert::eq(
            count($paymentMethods),
            1,
            sprintf('%d payment methods has been found with name "%s".', count($paymentMethods), $paymentMethodName),
        );

        return $paymentMethods[0];
    }
}
