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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Input;

use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;

final class VerifyShopUserStateProviderSpec extends ObjectBehavior
{
    function it_returns_null_when_operation_is_not_on_verify_customer_account(Operation $operation): void
    {
        $operation->getClass()->willReturn(\stdClass::class);

        $this->provide($operation)->shouldReturn(null);
    }

    function it_throws_invalid_argument_exception_when_no_token_is_provided(Operation $operation): void
    {
        $operation->getClass()->willReturn(VerifyShopUser::class);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation, ['token' => null]])
        ;
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation, ['token' => '']])
        ;
    }

    function it_returns_verify_customer_account_command_when_token_is_provided(
        ChannelInterface $channel,
        Operation $operation,
    ): void {
        $operation->getClass()->willReturn(VerifyShopUser::class);
        $channel->getCode()->willReturn('WEB');

        $this
            ->provide($operation, ['token' => 'TOKEN'], [ContextKeys::CHANNEL => $channel, ContextKeys::LOCALE_CODE => 'en_US'])
            ->shouldBeLike(new VerifyShopUser('TOKEN', 'WEB', 'en_US'))
        ;
    }
}
