<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type\Checkout;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SummaryType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @mixin SummaryType
 * 
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SummaryTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('order', ['sylius']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\Checkout\SummaryType');
    }

    function it_is_a_resource_form_type()
    {
        $this->shouldHaveType(AbstractResourceType::class);
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('notes', 'textarea', Argument::type('array'))->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_checkout_summary');
    }
}
