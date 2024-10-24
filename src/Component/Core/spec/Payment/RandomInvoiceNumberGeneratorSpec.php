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

namespace spec\Sylius\Component\Core\Payment;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Payment\InvoiceNumberGeneratorInterface;
use Sylius\Component\Core\Payment\RandomInvoiceNumberGenerator;

final class RandomInvoiceNumberGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(RandomInvoiceNumberGenerator::class);
    }

    function it_is_an_invoice_number_generator(): void
    {
        $this->shouldImplement(InvoiceNumberGeneratorInterface::class);
    }

    function it_generates_a_random_invoice_number(OrderInterface $order, PaymentInterface $payment): void
    {
        $this->generate($order, $payment)->shouldBeString();
    }
}
