<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Form\DataMapper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemQuantityDataMapperSpec extends ObjectBehavior
{
    function let(OrderItemQuantityModifierInterface $orderItemQuantityModifier, DataMapperInterface $propertyPathDataMapper)
    {
        $this->beConstructedWith($orderItemQuantityModifier, $propertyPathDataMapper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Form\DataMapper\OrderItemQuantityDataMapper');
    }

    function it_implements_data_mapper_interface()
    {
        $this->shouldImplement(DataMapperInterface::class);
    }

    function it_uses_property_path_data_mapper_while_mapping_data_to_forms($propertyPathDataMapper, FormInterface $form, OrderItemInterface $orderItem)
    {
        $propertyPathDataMapper->mapDataToForms($orderItem, [$form])->shouldBeCalled();

        $this->mapDataToForms($orderItem, [$form]);
    }
}
