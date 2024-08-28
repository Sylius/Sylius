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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\ContextBuilder;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Attribute\OrderItemIdAware;
use Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware;
use Sylius\Bundle\ApiBundle\Attribute\ShipmentIdAware;
use Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\HttpFoundation\Request;

final class UriVariablesAwareContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
    ): void {
        $this->beConstructedWith(
            $decoratedContextBuilder,
            ShipmentIdAware::class,
            'shipmentId',
            ShipmentInterface::class,
        );
    }

    function it_does_nothing_if_there_is_no_input_class(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn([])
        ;

        $this->createFromRequest($request, true, [])->shouldReturn([]);
    }

    function it_does_nothing_if_input_class_is_no_supported_attribute(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]])
        ;

        $this
            ->createFromRequest($request, true, [])
            ->shouldReturn(['input' => ['class' => \stdClass::class]])
        ;
    }

    function it_does_nothing_if_there_is_no_uri_variable(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
        HttpOperation $operation,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, ['operation' => $operation])
            ->willReturn(['input' => ['class' => ChooseShippingMethod::class]])
        ;
        $operation->getUriVariables()->willReturn([]);

        $this
            ->createFromRequest($request, true, ['operation' => $operation])
            ->shouldReturn(['input' => ['class' => ChooseShippingMethod::class]])
        ;
    }

    function it_does_nothing_if_there_is_different_uri_variable(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
        HttpOperation $operation,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, ['operation' => $operation])
            ->willReturn(['input' => ['class' => ChooseShippingMethod::class]])
        ;
        $uriVariable = new Link(fromClass: 'stdClass');
        $operation->getUriVariables()->willReturn([$uriVariable]);

        $this
            ->createFromRequest($request, true, ['operation' => $operation])
            ->shouldReturn(['input' => ['class' => ChooseShippingMethod::class]])
        ;
    }

    function it_set_shipment_id_as_a_constructor_argument(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
        HttpOperation $operation,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, ['operation' => $operation])
            ->willReturn(['input' => ['class' => ChooseShippingMethod::class], 'uri_variables' => ['shipmentId' => '123']])
        ;
        $uriVariable = new Link(fromClass: ShipmentInterface::class, parameterName: 'shipmentId');
        $operation->getUriVariables()->willReturn([$uriVariable]);

        $this
            ->createFromRequest($request, true, ['operation' => $operation])
            ->shouldReturn([
                'input' => ['class' => ChooseShippingMethod::class],
                'uri_variables' => ['shipmentId' => '123'],
                'default_constructor_arguments' => [
                    ChooseShippingMethod::class => ['shipmentId' => '123'],
                ],
            ])
        ;
    }

    function it_set_order_token_value_as_a_constructor_argument(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
        HttpOperation $operation,
    ): void {
        $this->beConstructedWith(
            $decoratedContextBuilder,
            OrderTokenValueAware::class,
            'orderTokenValue',
            OrderInterface::class,
        );

        $decoratedContextBuilder
            ->createFromRequest($request, true, ['operation' => $operation])
            ->willReturn(['input' => ['class' => CompleteOrder::class], 'uri_variables' => ['orderToken' => 'token123']])
        ;
        $uriVariable = new Link(fromClass: OrderInterface::class, parameterName: 'orderToken');
        $operation->getUriVariables()->willReturn([$uriVariable]);

        $this
            ->createFromRequest($request, true, ['operation' => $operation])
            ->shouldReturn([
                'input' => ['class' => CompleteOrder::class],
                'uri_variables' => ['orderToken' => 'token123'],
                'default_constructor_arguments' => [
                    CompleteOrder::class => ['orderTokenValue' => 'token123'],
                ],
            ])
        ;
    }

    function it_set_order_item_id_as_a_constructor_argument(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
        HttpOperation $operation,
    ): void {
        $this->beConstructedWith(
            $decoratedContextBuilder,
            OrderItemIdAware::class,
            'orderItemId',
            OrderItemInterface::class,
        );

        $decoratedContextBuilder
            ->createFromRequest($request, true, ['operation' => $operation])
            ->willReturn(['input' => ['class' => ChangeItemQuantityInCart::class], 'uri_variables' => ['orderItemId' => '23']])
        ;
        $uriVariable = new Link(fromClass: OrderItemInterface::class, parameterName: 'orderItemId');
        $operation->getUriVariables()->willReturn([$uriVariable]);

        $this
            ->createFromRequest($request, true, ['operation' => $operation])
            ->shouldReturn([
                'input' => ['class' => ChangeItemQuantityInCart::class],
                'uri_variables' => ['orderItemId' => '23'],
                'default_constructor_arguments' => [
                    ChangeItemQuantityInCart::class => ['orderItemId' => '23'],
                ],
            ])
        ;
    }
}
