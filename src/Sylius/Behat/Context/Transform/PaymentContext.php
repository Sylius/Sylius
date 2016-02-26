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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PaymentContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @param RepositoryInterface $paymentMethodRepository
     */
    public function __construct(RepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * @Transform /^"([^"]+)" payment$/
     */
    public function getPaymentMethodByName($paymentMethodName)
    {
        $paymentMethod = $this->paymentMethodRepository->findOneBy(['name' => $paymentMethodName]);
        if (null === $paymentMethod) {
            throw new \InvalidArgumentException(sprintf('Payment method with name "%s" does not exist.', $paymentMethodName));
        }

        return $paymentMethod;
    }
}
