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
use Sylius\Bundle\CoreBundle\Mailer\TwigMailerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;

class OrderConfirmationMailerSpec extends ObjectBehavior
{
    function let(TwigMailerInterface $mailer)
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

    function it_sends_order_confirmation_email(OrderInterface $order, UserInterface $user, TwigMailerInterface $mailer)
    {
        $parameters = array('template' => 'test-template.html.twig', 'from_email' => 'from@example.com');
        $this->beConstructedWith($mailer, $parameters);

        $user->getEmail()->willReturn('recipient@example.com');
        $order->getUser()->willReturn($user);

        $mailer->sendEmail(
            'test-template.html.twig',
            array('order' => $order),
            'from@example.com',
            'recipient@example.com'
        )->shouldBeCalled();

        $this->sendOrderConfirmation($order);
    }
}
