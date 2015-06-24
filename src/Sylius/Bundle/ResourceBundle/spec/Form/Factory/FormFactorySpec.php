<?php

namespace spec\Sylius\Bundle\ResourceBundle\Form\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Form\Guesser\FieldGuesser;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;

class FormFactorySpec extends ObjectBehavior
{
    function let(
        FormFactoryInterface $formFactory,
        FormRegistryInterface $formRegistry,
        \Sylius\Bundle\ResourceBundle\Form\Guesser\FieldGuesser $resourceFormFactory
    ) {
        $this->beConstructedWith($formFactory, $formRegistry, $resourceFormFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Factory\FormFactory');
    }

    function it_creates_an_instance($formFactory, Resource $resource)
    {
        $formFactory->create(
            Argument::type('spec\Sylius\Bundle\ResourceBundle\Form\Factory\FormType'),
            $resource
        )->shouldBeCalled();

        $this->create('spec\Sylius\Bundle\ResourceBundle\Form\Factory\FormType', $resource)
            ->shouldHaveType('Symfony\Component\Form\Form');
    }

    function it_gets_form_in_registry($formFactory, $formRegistry, FormType $formType, Resource $resource)
    {
        $formRegistry->hasType('form')->willreturn(true);
        $formRegistry->getType('form')->willReturn($formType);

        $formFactory->create(
            'form',
            $resource
        )->shouldBeCalled();

        $this->create('form', $resource)
            ->shouldHaveType('Symfony\Component\Form\Form');
    }

    function it_generate_the_form($resourceFormFactory, ObjectManager $objectManager, Resource $resource)
    {
        $resourceFormFactory->create($resource, $objectManager);

        $this->create(null, $resource, false ,$objectManager)
            ->shouldHaveType('Symfony\Component\Form\Form');

        // or
        $this->setObjectManager($objectManager);
        $this->create(null, $resource)
            ->shouldHaveType('Symfony\Component\Form\Form');
    }

    function it_create_named_form($formFactory, Resource $resource)
    {
        $formFactory->createNamed('', 'form', $resource, array('csrf_protection' => false))
            ->shouldBeCalled();

        $this->create('form', $resource, true)
            ->shouldHaveType('Symfony\Component\Form\Form');
    }
}

class Resource
{
}

class FormType
{
}
