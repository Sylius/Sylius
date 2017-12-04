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

namespace spec\Sylius\Component\Shipping\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Model\ShippingMethodTranslation;
use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;

final class ShippingMethodTranslationSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingMethodTranslation::class);
    }

    function it_implements_shipping_method_interface(): void
    {
        $this->shouldImplement(ShippingMethodTranslationInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_is_unnamed_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Shippable goods');
        $this->getName()->shouldReturn('Shippable goods');
    }

    function its_description_is_mutable(): void
    {
        $this->setDescription('Very good shipping, cheap price, good delivery time.');
        $this->getDescription()->shouldReturn('Very good shipping, cheap price, good delivery time.');
    }

    function it_returns_name_when_converted_to_string(): void
    {
        $this->setName('Shippable goods');
        $this->__toString()->shouldReturn('Shippable goods');
    }
}
