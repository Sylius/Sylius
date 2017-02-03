<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PaymentMethodContext implements Context
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
     * @Transform /^"([^"]+)" payment(s)?$/
     * @Transform :paymentMethod
     */
    public function getPaymentMethodByName($paymentMethodName)
    {
        $paymentMethods = $this->paymentMethodRepository->findByName($paymentMethodName, 'en_US');

        Assert::eq(
            count($paymentMethods),
            1,
            sprintf('%d payment methods has been found with name "%s".', count($paymentMethods), $paymentMethodName)
        );

        return $paymentMethods[0];
    }
}
