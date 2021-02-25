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

namespace spec\Sylius\Bundle\ApiBundle\DataTransformer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\EnrichableCommandInterface;
use Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareCommandInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;

final class LoggedInShopUserIdAwareCommandDataTransformerSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_supports_only_shop_user_id_commands(
        ShopUserIdAwareCommandInterface $shopUserIdAware,
        EnrichableCommandInterface $commandAwareDataTransformer
    ): void {
        $this->supportsTransformation($shopUserIdAware)->shouldReturn(true);
        $this->supportsTransformation($commandAwareDataTransformer)->shouldReturn(false);
    }

    function it_sets_current_shop_user_id(
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        ShopUserIdAwareCommandInterface $shopUserIdAwareCommand
    ): void {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getId()->willReturn(2);

        $shopUserIdAwareCommand->setShopUserId(2)->shouldBeCalled();

        $this->transform($shopUserIdAwareCommand, '', [])->shouldReturn($shopUserIdAwareCommand);
    }

    function it_does_nothing_if_logged_in_user_is_not_shop_user(
        UserContextInterface $userContext,
        UserInterface $user,
        ShopUserIdAwareCommandInterface $shopUserIdAwareCommand
    ): void {
        $userContext->getUser()->willReturn($user);

        $shopUserIdAwareCommand->setShopUserId(Argument::any())->shouldNotBeCalled();

        $this->transform($shopUserIdAwareCommand, '', [])->shouldReturn($shopUserIdAwareCommand);
    }
}
