<?php

namespace spec\Sylius\Bundle\ArchetypeBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class ArchetypeTranslationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ArchetypeTranslation', array('sylius'), 'subject');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeTranslationType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType');
    }

    function it_buils_a_form(FormBuilderInterface $builder)
    {
        $builder->add('name', 'text', Argument::type('array'))->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_subject_archetype_translation');
    }
}
