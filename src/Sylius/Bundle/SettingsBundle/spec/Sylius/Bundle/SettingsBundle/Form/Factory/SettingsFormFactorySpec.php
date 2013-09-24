<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Form\Factory;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SettingsFormFactorySpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface $schemaRegistry
     * @param Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    function let($schemaRegistry, $formFactory)
    {
        $this->beConstructedWith($schemaRegistry, $formFactory);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Form\Factory\SettingsFormFactory');
    }

    function it_should_implement_settings_form_factory_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Form\Factory\SettingsFormFactoryInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder                  $formBuilder
     * @param Symfony\Component\Form\Form                         $form
     * @param Sylius\Bundle\SettingsBundle\Schema\SchemaInterface $schema
     */
    function it_should_create_a_form_for_given_schema_namespace($schemaRegistry, $schema, $formFactory, $formBuilder, $form)
    {
        $schemaRegistry->getSchema('general')->willReturn($schema);
        $formFactory->createBuilder('form', null, array('data_class' => null))->willReturn($formBuilder);
        $schema->buildForm($formBuilder)->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->willReturn($form);

        $this->create('general')->shouldReturn($form);
    }
}
