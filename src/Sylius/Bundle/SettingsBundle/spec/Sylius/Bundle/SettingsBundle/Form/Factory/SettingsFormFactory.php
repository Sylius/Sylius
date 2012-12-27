<?php

namespace spec\Sylius\Bundle\SettingsBundle\Form\Factory;

use PHPSpec2\ObjectBehavior;

/**
 * Settings form factory spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SettingsFormFactory extends ObjectBehavior
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

    /**
     * @param Symfony\Component\Form\FormBuilder     $formBuilder
     * @param Symfony\Component\Form\Form            $form
     * @param Symfony\Component\Form\SchemaInterface $schema
     */
    function it_should_create_a_form_for_given_schema_namespace($schemaRegistry, $schema, $formFactory, $formBuilder, $form)
    {
        $schemaRegistry->getSchema('general-settings')->willReturn($schema);
        $formFactory->createBuilder('form', null, array('data_class' => null))->willReturn($formBuilder);
        $schema->build($formBuilder)->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->willReturn($form);

        $this->create('general-settings')->shouldReturn($form);
    }
}
