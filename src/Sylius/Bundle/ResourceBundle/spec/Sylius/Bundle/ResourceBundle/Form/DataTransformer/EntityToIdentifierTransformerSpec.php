<?php

namespace spec\Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

// Since the root namespace "spec" is not in our autoload
require_once __DIR__.DIRECTORY_SEPARATOR.'FakeEntity.php';

class EntityToIdentifierTransformerSpec extends ObjectBehavior
{
    function let(ObjectRepository $repository)
    {
        $repository->getClassName()->willReturn('spec\Sylius\Bundle\ResourceBundle\Form\DataTransformer\FakeEntity');
        $this->beConstructedWith($repository, 'id');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\DataTransformer\EntityToIdentifierTransformer');
    }

    function it_does_not_transform_null_value(ObjectRepository $repository)
    {
        $value = null;

        $repository->findOneBy(Argument::any())->shouldNotBeCalled();

        $this->transform($value)->shouldReturn(null);
    }

    function it_throws_an_exception_on_non_existing_entity(ObjectRepository $repository)
    {
        $value = 6;

        $repository->findOneBy(array('id' => $value))->shouldBeCalled()->willReturn(null);

        $this->shouldThrow('Symfony\Component\Form\Exception\TransformationFailedException')->duringTransform($value);
    }

    function it_transforms_identifier_in_entity(ObjectRepository $repository, FakeEntity $entity)
    {
        $value = 5;

        $repository->findOneBy(array('id' => $value))->shouldBeCalled()->willReturn($entity);

        $this->transform($value)->shouldReturn($entity);
    }

    function it_does_not_reverse_null_value()
    {
        $value = null;

        $this->reverseTransform($value)->shouldReturn('');
    }

    function it_reverses_entity_in_identifier(FakeEntity $value)
    {
        $id = 6;
        $value->getId()->shouldBeCalled()->willReturn($id);

        $this->reverseTransform($value)->shouldReturn($id);
    }
}
