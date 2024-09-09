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

namespace spec\Sylius\Bundle\CoreBundle\CommandHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Command\ResendOrderConfirmationEmail;
use Sylius\Bundle\CoreBundle\CommandHandler\ResendOrderConfirmationEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

final class ResendOrderConfirmationEmailHandlerSpec extends ObjectBehavior
{
    function let(OrderEmailManagerInterface $orderEmailManager, RepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderEmailManager, $orderRepository);
    }

    function it_is_a_message_handler(): void
    {
        $messageHandlerAttributes = (new \ReflectionClass(ResendOrderConfirmationEmailHandler::class))
            ->getAttributes(AsMessageHandler::class);

        Assert::count($messageHandlerAttributes, 1);
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
