<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\Type\Configuration\AttributeType;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Laurent Paganin-Gioanni <l.paganin@algo-factory.com>
 */
class SelectAttributeConfigurationTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeConfigurationType');
    }

    function it_is_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_configuration_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('options', 'collection', [
                'type' => 'text',
                'label' => 'sylius.form.attribute_type_configuration.select.values',
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->willReturn($builder)
        ;

        $builder
            ->add('multiple', 'checkbox', [
                'label' => 'sylius.attribute_type_configuration.select.multiple',
            ])
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_attribute_type_configuration_select');
    }
}
