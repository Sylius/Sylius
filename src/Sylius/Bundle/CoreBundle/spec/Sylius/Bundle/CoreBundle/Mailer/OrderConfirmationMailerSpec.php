<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Mailer;

use PhpSpec\ObjectBehavior;

class OrderConfirmationMailerSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CoreBundle\Mailer\TwigMailerInterface $mailer
     */
    function let($mailer)
    {
        $params = array(
            'template' => 'order-receipt-template',
            'from_email' => array('info@sylius.org' => 'Sylius Website')
        );

        $this->beConstructedWith($mailer, $params);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Mailer\OrderConfirmationMailer');
    }

    function it_implements_correct_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Mailer\OrderConfirmationMailerInterface');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $order
     * @param Sylius\Bundle\CoreBundle\Model\UserInterface $user
     */
    function it_sends_order_confirmation_email($order, $user, $mailer)
    {
        $order->getUser()->shouldBeCalled()->willReturn($user);
        $context = array('order' => $order);

        $this->sendOrderConfirmation($order);
    }
}