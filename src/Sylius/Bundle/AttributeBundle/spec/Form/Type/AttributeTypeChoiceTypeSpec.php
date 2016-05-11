<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeTypeChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['test' => 'Test attribute type']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\Type\AttributeTypeChoiceType');
    }

    function it_is_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_configures_proper_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_translation_domain' => false,
            'choices' => ['test' => 'Test attribute type']
        ])->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_choice_as_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_attribute_type_choice');
    }
}
