<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\EventHandler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\SendShopUserVerificationEmail;
use Sylius\Bundle\ApiBundle\Event\ShopUserRegistered;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ShopUserRegisteredHandlerSpec extends ObjectBehavior
{
    function let(
        MessageBusInterface $commandBus,
        ChannelRepositoryInterface $channelRepository,
        UserRepositoryInterface $shopUserRepository,
        GeneratorInterface $tokenGenerator
    ): void {
        $this->beConstructedWith($commandBus, $channelRepository, $shopUserRepository, $tokenGenerator);
    }

    function it_dispatches_command_when_email_with_verification_token_is_required(
        UserRepositoryInterface $shopUserRepository,
        ShopUserInterface $shopUser,
        MessageBusInterface $commandBus
    ): void {
        $shopUserRepository->findOneByEmail('smith@sylius.com')->willReturn($shopUser);
        $shopUser->getEmailVerificationToken()->willReturn('TOKEN');

        $command = new SendShopUserVerificationEmail('smith@sylius.com',  'en_US', 'CHANNEL_CODE');
        $commandBus->dispatch($command)->shouldBeCalled()->willReturn(new Envelope($command));

        $this(new ShopUserRegistered('smith@sylius.com','CHANNEL_CODE', 'en_US'));
    }

    function it_does_nothing_if_email_with_verification_token_is_not_required(
        UserRepositoryInterface $shopUserRepository,
        ShopUserInterface $shopUser,
        MessageBusInterface $commandBus
    ): void {
        $shopUserRepository->findOneByEmail('smith@sylius.com')->willReturn($shopUser);
        $shopUser->getEmailVerificationToken()->willReturn(null);

        $commandBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this(new ShopUserRegistered('smith@sylius.com','CHANNEL_CODE', 'en_US'));
    }
}
