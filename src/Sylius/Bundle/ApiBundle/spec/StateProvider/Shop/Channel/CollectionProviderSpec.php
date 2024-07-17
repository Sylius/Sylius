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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\Channel;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;

final class CollectionProviderSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    function it_provides_channel(
        ChannelInterface $channel,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: Channel::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this
            ->provide($operation, [], ['sylius_api_channel' => $channel])
            ->shouldBeLike([$channel])
        ;
    }

    function it_throws_an_exception_when_operation_class_is_not_channel(
        Operation $operation,
    ): void {
        $operation->getClass()->willReturn(\stdClass::class);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_get_collection(
        Operation $operation,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation->getClass()->willReturn(Channel::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_in_shop_api_section(
        ChannelInterface $channel,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: Channel::class);
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation, [], ['sylius_api_channel' => $channel]])
        ;
    }

    function it_throws_an_exception_when_context_does_not_have_channel(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: Channel::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation, [], []])
        ;
    }
}
