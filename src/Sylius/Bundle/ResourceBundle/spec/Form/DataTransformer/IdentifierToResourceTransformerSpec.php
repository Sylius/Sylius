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
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

// Since the root namespace "spec" is not in our autoload
require_once __DIR__ . DIRECTORY_SEPARATOR . 'FakeEntity.php';

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class IdentifierToResourceTransformerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository, 'id');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\DataTransformer\IdentifierToResourceTransformer');
    }

    function it_implements_data_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_does_not_transform_null_value($repository)
    {
        $repository->findOneBy(Argument::any())->shouldNotBeCalled();

        $this->transform(null)->shouldReturn(null);
    }

    function it_throws_an_exception_on_non_existing_entity($repository)
    {
        $repository->getClassName()->willReturn(FakeEntity::class);
        $repository->findOneBy(['id' => 6])->shouldBeCalled()->willReturn(null);

        $this->shouldThrow(TransformationFailedException::class)->during('transform', array(6));
    }

    function it_transforms_identifier_in_entity($repository, FakeEntity $entity)
    {
        $repository->findOneBy(['id' => 5])->willReturn($entity);
        $this->transform(5)->shouldReturn($entity);
    }

    function it_reverses_null_value_to_empty_string()
    {
        $this->reverseTransform(null)->shouldReturn('');
    }

    function it_reverses_entity_in_identifier($repository, FakeEntity $value)
    {
        $repository->getClassName()->willReturn(FakeEntity::class);
        $value->getId()->willReturn(6);

        $this->reverseTransform($value)->shouldReturn(6);
    }
}
