<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Core\Test\Services;

use PhpSpec\ObjectBehavior;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Model\PaymentInterface;
use Sylius\Core\Payment\InvoiceNumberGeneratorInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class RandomInvoiceNumberGeneratorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Core\Test\Services\RandomInvoiceNumberGenerator');
    }

    public function it_is_invoice_number_generator()
    {
        $this->shouldImplement(InvoiceNumberGeneratorInterface::class);
    }

    public function it_generates_random_invoice_number(OrderInterface $order, PaymentInterface $payment)
    {
        $this->generate($order, $payment)->shouldBeString();
    }
}
