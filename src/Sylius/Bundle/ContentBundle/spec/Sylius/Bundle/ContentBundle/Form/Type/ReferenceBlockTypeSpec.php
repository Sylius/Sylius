<?php

namespace spec\Sylius\Bundle\ContentBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class ReferenceBlockTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('My\Resource\Model', array('validation_group'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Form\Type\ReferenceBlockType');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('id', 'text', Argument::type('array'))->willReturn($builder);
        $builder->add('title', 'text', Argument::type('array'))->willReturn($builder);
        $builder->add('body', 'textarea', Argument::type('array'))->willReturn($builder);

        $this->buildForm($builder);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_reference_block');
    }
}
