<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Piotr Walków <walkow.piotr@gmailcom>
 */
class TaxationRemoverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\OrderProcessing\TaxationRemover');
    }

    function it_implements_Sylius_taxation_remover_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\TaxationRemoverInterface');
    }

    function it_removes_existing_tax_adjustments(
        OrderInterface $order
    ) {
        $order->removeAdjustments(Argument::any())->shouldBeCalled();

        $order->calculateTotal()->shouldBeCalled();

        $this->removeTaxes($order);
    }
}
