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
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType;
use Sylius\Component\Addressing\Model\ZoneMember;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneMemberTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ZoneMember', ['sylius']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_is_a_Sylius_zone_member_type()
    {
        $this->shouldHaveType(ZoneMemberType::class);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_zone_member');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('code', 'sylius_zone_code_choice', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, ['zone_type' => 'zone']);
    }

    function it_configures_options(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'empty_value' => 'sylius.form.zone_member.select',
                'data_class' => ZoneMember::class,
                'zone_type' => 'country',
            ])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }
}
