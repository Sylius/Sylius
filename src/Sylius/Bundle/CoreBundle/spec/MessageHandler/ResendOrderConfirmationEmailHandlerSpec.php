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

namespace spec\Sylius\Bundle\CoreBundle\MessageHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EmailManager\OrderEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Message\ResendOrderConfirmationEmail;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ResendOrderConfirmationEmailHandlerSpec extends ObjectBehavior
{
    function let(OrderEmailManagerInterface $orderEmailManager, RepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderEmailManager, $orderRepository);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_resends_order_confirmation_email(
        OrderEmailManagerInterface $orderEmailManager,
        RepositoryInterface $orderRepository,
    ): void {
        $order = new Order();

        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn($order);
        $orderEmailManager->resendConfirmationEmail($order)->shouldBeCalled();

        $this->__invoke(new ResendOrderConfirmationEmail('TOKEN'));
    }

    function it_throws_not_found_exception_when_order_not_found(
        RepositoryInterface $orderRepository,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'NON_EXISTING_TOKEN'])->willReturn(null);

        $this
            ->shouldThrow(NotFoundHttpException::class)
            ->during('__invoke', [new ResendOrderConfirmationEmail('NON_EXISTING_TOKEN')])
        ;
    }
}
