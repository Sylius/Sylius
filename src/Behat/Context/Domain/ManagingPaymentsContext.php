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

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final class ManagingPaymentsContext implements Context
{
    public function __construct(private PaymentRepositoryInterface $paymentRepository)
    {
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
