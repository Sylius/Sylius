<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;

// Since the root namespace "spec" is not in our autoload
require_once __DIR__ . DIRECTORY_SEPARATOR . 'FakeEntity.php';

class ResourceToIdentifierTransformerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository, 'id');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer');
    }

    function it_implements_data_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_does_not_reverse_null_value($repository)
    {
        $repository->findOneBy(Argument::any())->shouldNotBeCalled();

        $this->reverseTransform(null)->shouldReturn(null);
    }

    function it_throws_an_exception_on_non_existing_entity($repository)
    {
        $repository->getClassName()->willReturn(FakeEntity::class);
        $repository->findOneBy(['id' => 6])->willReturn(null);

        $this->shouldThrow(TransformationFailedException::class)->during('reverseTransform', array(6));
    }

    function it_reverses_identifier_in_entity($repository, FakeEntity $entity)
    {
        $repository->getClassName()->willReturn(FakeEntity::class);
        $repository->findOneBy(['id' => 5])->willReturn($entity);

        $this->reverseTransform(5)->shouldReturn($entity);
    }

    function it_transforms_null_value_into_empty_string()
    {
        $this->transform(null)->shouldReturn('');
    }

    function it_transforms_entity_in_identifier(FakeEntity $value, $repository)
    {
        $repository->getClassName()->willReturn(FakeEntity::class);
        $value->getId()->willReturn(6);

        $this->transform($value)->shouldReturn(6);
    }
}
