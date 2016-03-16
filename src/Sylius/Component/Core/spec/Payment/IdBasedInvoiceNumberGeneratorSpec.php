<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Payment;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Payment\InvoiceNumberGeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class IdBasedInvoiceNumberGeneratorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Payment\IdBasedInvoiceNumberGenerator');
    }

    public function it_is_invoice_number_generator()
    {
        $this->shouldImplement(InvoiceNumberGeneratorInterface::class);
    }

    public function it_generates_invoice_number_based_on(OrderInterface $order, PaymentInterface $payment)
    {
        $order->getId()->willReturn('001');
        $payment->getId()->willReturn('1');

        $this->generate($order, $payment)->shouldReturn('001-1');
    }
}
