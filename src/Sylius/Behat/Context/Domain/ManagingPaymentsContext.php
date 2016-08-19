<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingPaymentsContext implements Context
{
    /**
     * @var PaymentRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @param PaymentRepositoryInterface $paymentRepository
     */
    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @Then /^there should be no ("[^"]+" payments) in the registry$/
     */
    public function paymentShouldNotExistInTheRegistry(PaymentMethodInterface $paymentMethod)
    {
        $payments = $this->paymentRepository->findBy(['method' => $paymentMethod]);

        Assert::same($payments, []);
    }
}
