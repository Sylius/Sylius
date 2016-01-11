<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Sylius\Bundle\ResourceBundle\Fixture\Model\Foo;
use spec\Sylius\Bundle\ResourceBundle\Fixture\Model\FooInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\SyliusTranslationResource;
use Sylius\Component\Resource\Factory\Factory;

require_once __DIR__ . '/../../Fixture/Model/FooInterface.php';
require_once __DIR__ . '/../../Fixture/Model/Foo.php';

/**
 * @mixin SyliusTranslationResource
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SyliusTranslationResourceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Foo::class, FooInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Configuration\SyliusTranslationResource');
    }

    function it_correctly_assigns_model_passed_to_the_constructor()
    {
        $this->beConstructedWith(Foo::class);

        $this->getModelClass()->shouldReturn(Foo::class);
    }

    function it_correctly_assigns_interface_passed_to_the_constructor()
    {
        $this->beConstructedWith(Foo::class, FooInterface::class);

        $this->getInterfaceClass()->shouldReturn(FooInterface::class);
    }

    function it_has_no_factory_by_default()
    {
        $this->getFactoryClass()->shouldReturn(null);
    }

    function it_sets_resource_factory_as_used_if_using_default()
    {
        $this->useDefaultFactory();

        $this->getFactoryClass()->shouldReturn(Factory::class);
    }

    function it_uses_given_factory()
    {
        $this->useFactory(\stdClass::class);

        $this->getFactoryClass()->shouldReturn(\stdClass::class);
    }

    function it_has_no_controller_by_default()
    {
        $this->getControllerClass()->shouldReturn(null);
    }

    function it_sets_resource_controller_as_used_if_using_default()
    {
        $this->useDefaultController();

        $this->getControllerClass()->shouldReturn(ResourceController::class);
    }

    function it_uses_given_controller()
    {
        $this->useController(\stdClass::class);

        $this->getControllerClass()->shouldReturn(\stdClass::class);
    }

    function it_has_no_repository_by_default()
    {
        $this->getRepositoryClass()->shouldReturn(null);
    }

    function it_does_not_set_anything_as_used_if_using_default()
    {
        $this->useDefaultRepository();

        $this->getRepositoryClass()->shouldReturn(null);
    }

    function it_uses_given_repository()
    {
        $this->useRepository(\stdClass::class);

        $this->getRepositoryClass()->shouldReturn(\stdClass::class);
    }

    function it_adds_form_without_validation_groups()
    {
        $this->addForm('name', \stdClass::class);

        $this->getFormsClasses()->shouldReturn(['name' => \stdClass::class]);
        $this->getValidationGroups()->shouldReturn([]);
    }

    function it_adds_form_with_validation_groups()
    {
        $this->addForm('name', \stdClass::class, ['validation group']);

        $this->getFormsClasses()->shouldReturn(['name' => \stdClass::class]);
        $this->getValidationGroups()->shouldReturn(['name' => ['validation group']]);
    }

    function it_adds_default_form_if_null_name_is_provided()
    {
        $this->addForm(null, \stdClass::class);

        $this->getFormsClasses()->shouldReturn(['default' => \stdClass::class]);
        $this->getValidationGroups()->shouldReturn([]);
    }

    function it_adds_default_form_if_empty_name_is_provided()
    {
        $this->addForm('', \stdClass::class);

        $this->getFormsClasses()->shouldReturn(['default' => \stdClass::class]);
        $this->getValidationGroups()->shouldReturn([]);
    }

    function it_has_no_options_by_default()
    {
        $this->getOptions()->shouldReturn([]);
    }

    function its_options_are_mutable()
    {
        $this->setOptions(['option' => 'value']);

        $this->getOptions()->shouldReturn(['option' => 'value']);
    }

    function it_has_no_translatable_fields_by_default()
    {
        $this->getTranslatableFields()->shouldReturn([]);
    }

    function its_translatable_fields_can_be_set()
    {
        $this->setTranslatableFields(['field1', 'field2']);

        $this->getTranslatableFields()->shouldReturn(['field1', 'field2']);
    }
}
