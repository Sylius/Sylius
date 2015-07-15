<?php

namespace spec\Sylius\Bundle\ApiBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class ClientTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Client', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Form\Type\ClientType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType');
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('secret', 'text', Argument::type('array'))->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_api_client');
    }
}
