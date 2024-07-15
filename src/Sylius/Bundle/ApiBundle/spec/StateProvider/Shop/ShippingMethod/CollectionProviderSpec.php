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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\ShippingMethod;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;

final class CollectionProviderSpec extends ObjectBehavior
{
    function let(
        SectionProviderInterface $sectionProvider,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
    ): void {
        $this->beConstructedWith($sectionProvider, $shippingMethodRepository);
    }

    function it_provides_shipping_methods(
        SectionProviderInterface $sectionProvider,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ChannelInterface $channel,
        ShippingMethodInterface $method,
    ): void {
        $operation = new GetCollection(class: ShippingMethod::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $shippingMethodRepository->findEnabledForChannel($channel)->willReturn([$method->getWrappedObject()]);

        $this->provide($operation, [], ['sylius_api_channel' => $channel])->shouldReturn([$method->getWrappedObject()]);
    }

    function it_throws_an_exception_when_resource_is_not_a_shipping_method_interface(): void
    {
        $operation = new GetCollection(class: \stdClass::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_when_operation_is_not_get_collection(): void
    {
        $operation = new Get(class: ShippingMethod::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_when_operation_is_not_in_shop_api_section(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: ShippingMethod::class);
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }
}
