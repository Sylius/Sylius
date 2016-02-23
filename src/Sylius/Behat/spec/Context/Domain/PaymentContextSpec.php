<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class PaymentContextSpec extends ObjectBehavior
{
    function let(RepositoryInterface $paymentRepository)
    {
        $this->beConstructedWith($paymentRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Domain\PaymentContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_checks_if_a_payment_exists_in_repository(
        RepositoryInterface $paymentRepository,
        PaymentMethodInterface $cashOnDeliveryPaymentMethod
    ) {
        $paymentRepository->findBy(['method' => $cashOnDeliveryPaymentMethod])->willReturn([]);

        $this->paymentShouldNotExistInTheRegistry($cashOnDeliveryPaymentMethod);
    }

    function it_throws_an_exception_if_payment_still_exist(
        RepositoryInterface $paymentRepository,
        PaymentMethodInterface $paypalPaymentMethod,
        PaymentInterface $payment
    ) {
        $paymentRepository->findBy(['method' => $paypalPaymentMethod])->willReturn([$payment]);

        $this->shouldThrow(NotEqualException::class)->during('paymentShouldNotExistInTheRegistry', [$paypalPaymentMethod]);
    }
}
