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
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ZoneMemberCollectionTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberCollectionType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_zone_member_collection');
    }

    function it_defines_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false,
        ))->shouldBeCalled();

        $this->configureOptions($resolver);
    }
}
