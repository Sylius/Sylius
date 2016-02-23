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
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PaymentContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $paymentRepository;

    /**
     * @param RepositoryInterface $paymentRepository
     */
    public function __construct(RepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @Then /^there should be no ("[^"]+" payments) in the registry$/
     */
    public function paymentShouldNotExistInTheRegistry(PaymentMethodInterface $paymentMethod)
    {
        $payments = $this->paymentRepository->findBy(['method' => $paymentMethod]);

        expect($payments)->toBe([]);
    }
}
