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

namespace spec\Sylius\Bundle\ShippingBundle\Validator\GroupsGenerator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Component\Form\FormInterface;

final class ShippingMethodConfigurationGroupsGeneratorSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            'flat_rate' => ['rate', 'sylius'],
            'per_unit_rate' => ['rate', 'sylius'],
        ]);
    }

    function it_throws_error_if_invalid_object_is_passed(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new \stdClass()])
        ;
    }

    function it_returns_shipping_method_configuration_validation_groups(
        ShippingMethodInterface $shippingMethod,
    ): void {
        $shippingMethod->getCalculator()->willReturn('flat_rate');

        $this($shippingMethod)->shouldReturn(['rate', 'sylius']);
    }

    function it_returns_default_validation_groups(
        ShippingMethodInterface $shippingMethod,
    ): void {
        $shippingMethod->getCalculator()->willReturn(null);

        $this($shippingMethod)->shouldReturn(['sylius']);
    }

    function it_returns_gateway_config_validation_groups_if_it_is_shipping_method_form(
        FormInterface $form,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $form->getData()->willReturn($shippingMethod);
        $shippingMethod->getCalculator()->willReturn('per_unit_rate');

        $this($form)->shouldReturn(['rate', 'sylius']);
    }
}
