<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\OrderBundle\Form\Type\OrderItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class OrderItemTypeSpec extends ObjectBehavior
{
    function let(DataMapperInterface $orderItemQuantityDataMapper)
    {
        $this->beConstructedWith('OrderItem', ['sylius'], $orderItemQuantityDataMapper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderItemType::class);
    }

    function it_is_a_form_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_a_form_with_quantity_and_unit_price_fields(
        DataMapperInterface $orderItemQuantityDataMapper,
        FormBuilderInterface $builder
    ) {
        $builder
            ->add('quantity', 'integer', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('unitPrice', 'sylius_money', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->setDataMapper($orderItemQuantityDataMapper)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_defines_an_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'OrderItem',
                'validation_groups' => ['sylius'],
            ])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_order_item');
    }
}
