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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PaymentContext implements Context
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
     */
    public function getPaymentMethodByName($paymentMethodName)
    {
        $paymentMethod = $this->paymentMethodRepository->findOneByName($paymentMethodName);
        if (null === $paymentMethod) {
            throw new \InvalidArgumentException(sprintf('Payment method with name "%s" does not exist.', $paymentMethodName));
        }

        return $paymentMethod;
    }
}
