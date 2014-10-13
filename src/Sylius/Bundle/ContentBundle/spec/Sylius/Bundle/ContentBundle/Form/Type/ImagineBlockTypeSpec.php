<?php

namespace spec\Sylius\Bundle\ContentBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\Test\FormBuilderInterface;

class ImagineBlockTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('My\Resource\Model', array('validation_group'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ContentBundle\Form\Type\ImagineBlockType');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('parentDocument', null, Argument::type('array'))->willReturn($builder);
        $builder->add('name', 'text', Argument::type('array'))->willReturn($builder);
        $builder->add('label', 'text', Argument::type('array'))->willReturn($builder);
        $builder->add('linkUrl', 'text', Argument::type('array'))->willReturn($builder);
        $builder->add('actionName', 'text', Argument::type('array'))->willReturn($builder);
        $builder->add('filter', 'choice', Argument::type('array'))->willReturn($builder);
        $builder->add('image', 'cmf_media_image', Argument::type('array'))->willReturn($builder);

        $this->buildForm($builder);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_imagine_block');
    }
}
