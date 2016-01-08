<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ZoneTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Zone', ['sylius'], ['shipping', 'pricing']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\Type\ZoneType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_zone');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('name', 'text', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('type', 'sylius_zone_type_choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('scope', 'choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('members', 'sylius_zone_member_collection', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_defines_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class'        => 'Zone',
                    'validation_groups' => ['sylius']
                ]
            )
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }
}
