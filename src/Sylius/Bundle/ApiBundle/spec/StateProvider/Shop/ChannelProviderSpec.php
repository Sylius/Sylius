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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelProviderSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    function it_provides_channel(ChannelInterface $channel, SectionProviderInterface $sectionProvider): void
    {
        $operation = new GetCollection(class: Channel::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this
            ->provide($operation, [], ['sylius_api_channel' => $channel])
            ->shouldBeLike([$channel])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_get_collection(Operation $operation, SectionProviderInterface $sectionProvider): void
    {
        $operation->getClass()->willReturn(Channel::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this->shouldThrow(\RuntimeException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_exception_if_operation_is_not_shop(ChannelInterface $channel, SectionProviderInterface $sectionProvider): void
    {
        $operation = new GetCollection(class: Channel::class);
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\RuntimeException::class)
            ->during('provide', [$operation, [], ['sylius_api_channel' => $channel]])
        ;
    }
}
