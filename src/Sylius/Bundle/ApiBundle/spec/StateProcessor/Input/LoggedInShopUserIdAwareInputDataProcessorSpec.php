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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Input;

use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class LoggedInShopUserIdAwareInputDataProcessorSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_only_supports_data_implementing_shop_user_id_aware_interface(
        Operation $operation,
        ShopUserIdAwareInterface $data,
    ): void {
        $this->supports([], $operation)->shouldReturn(false);
        $this->supports($data, $operation)->shouldReturn(true);
    }

    function it_throw_exception_when_processing_data_that_does_not_implement_shop_user_id_aware_interface(
        Operation $operation,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [[], $operation])
        ;
    }

    function it_does_nothing_when_shop_user_cannot_be_found(
        UserContextInterface $userContext,
        ShopUserIdAwareInterface $data,
        Operation $operation,
    ): void {
        $userContext->getUser()->willReturn(null);

        $data->setShopUserId(Argument::any())->shouldNotBeCalled();

        $this->process($data, $operation)->shouldReturn([$data, $operation, [], []]);
    }

    function it_sets_shop_user_id_when_user_is_found(
        UserContextInterface $userContext,
        ShopUserIdAwareInterface $data,
        Operation $operation,
        ShopUserInterface $user,
    ): void {
        $user->getId()->willReturn(12);
        $userContext->getUser()->willReturn($user);

        $data->setShopUserId(12)->shouldBeCalled();

        $this->process($data, $operation)->shouldReturn([$data, $operation, [], []]);
    }
}
