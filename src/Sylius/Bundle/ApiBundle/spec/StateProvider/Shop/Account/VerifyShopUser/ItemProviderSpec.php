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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\Account\VerifyShopUser;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ItemProviderSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    function it_returns_verify_customer_account_command_when_token_is_provided(
        ChannelInterface $channel,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new Patch(class: VerifyShopUser::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $channel->getCode()->willReturn('WEB');

        $this
            ->provide($operation, ['token' => 'TOKEN'], [ContextKeys::CHANNEL => $channel, ContextKeys::LOCALE_CODE => 'en_US'])
            ->shouldBeLike(new VerifyShopUser('TOKEN', 'WEB', 'en_US'))
        ;
    }

    function it_throws_an_exception_when_operation_class_is_not_verify_shop_user(
        Operation $operation,
    ): void {
        $operation->getClass()->willReturn(\stdClass::class);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_patch(
        Operation $operation,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation->getClass()->willReturn(VerifyShopUser::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_in_shop_api_section(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new Patch(class: VerifyShopUser::class);
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_invalid_argument_exception_when_no_token_is_provided(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new Patch(class: VerifyShopUser::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

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
}
