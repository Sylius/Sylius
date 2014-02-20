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
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SettingsFormFactorySpec extends ObjectBehavior
{
    function let(
        SchemaRegistryInterface $schemaRegistry,
        FormFactoryInterface $formFactory
    )
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

    function it_should_create_a_form_for_given_schema_namespace(
        $schemaRegistry,
        SchemaInterface $schema,
        $formFactory,
        FormBuilder $formBuilder,
        Form $form
    )
    {
        $schemaRegistry->getSchema('general')->willReturn($schema);
        $formFactory->createBuilder('form', null, array('data_class' => null))->willReturn($formBuilder);
        $schema->buildForm($formBuilder)->shouldBeCalled()->willReturn($formBuilder);
        $formBuilder->getForm()->willReturn($form);

        $this->create('general')->shouldReturn($form);
    }
}
