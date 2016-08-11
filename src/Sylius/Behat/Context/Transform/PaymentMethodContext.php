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
        $paymentMethod = $this->paymentMethodRepository->findOneByName($paymentMethodName);

        Assert::notNull(
            $paymentMethod,
            sprintf('Payment method with name "%s" does not exist', $paymentMethodName)
        );

        return $paymentMethod;
    }
}
