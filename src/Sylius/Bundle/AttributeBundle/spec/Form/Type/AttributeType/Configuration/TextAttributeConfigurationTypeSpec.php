<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\Type\AttributeType;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TextAttributeConfigurationTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\TextAttributeConfigurationType');
    }

    function it_is_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_validations_form(FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('min', 'number', ['label' => 'sylius.form.attribute_type_configuration.text.min'])
            ->willReturn($formBuilder)
        ;

        $formBuilder
            ->add('max', 'number', ['label' => 'sylius.form.attribute_type_configuration.text.max'])
            ->willReturn($formBuilder)
        ;

        $this->buildForm($formBuilder, []);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_attribute_type_configuration_text');
    }
}
