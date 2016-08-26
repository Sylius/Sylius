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

// Since the root namespace "spec" is not in our autoload
require_once __DIR__.DIRECTORY_SEPARATOR.'FakeEntity.php';

final class ResourceToIdentifierTransformerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $repository->getClassName()->willReturn('spec\Sylius\Bundle\ResourceBundle\Form\DataTransformer\FakeEntity');
        $this->beConstructedWith($repository, 'id');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer');
    }

    function it_does_not_reverses_null_value(RepositoryInterface $repository)
    {
        $repository->findOneBy(Argument::any())->shouldNotBeCalled();

        $this->reverseTransform(null)->shouldReturn(null);
    }

    function it_throws_an_exception_on_non_existing_resource(RepositoryInterface $repository)
    {
        $repository->findOneBy(['id' => 6])->willReturn(null);

        $this->shouldThrow(TransformationFailedException::class)->during('reverseTransform', [6]);
    }

    function it_reverse_transform_identifier_to_resource(RepositoryInterface $repository, FakeEntity $resource)
    {
        $repository->findOneBy(['id' => 5])->willReturn($resource);

        $this->reverseTransform(5)->shouldReturn($resource);
    }

    function it_transforms_null_value_to_empty_string()
    {
        $this->transform(null)->shouldReturn('');
    }

    function it_transforms_resource_in_identifier(FakeEntity $value)
    {
        $value->getId()->willReturn(6);

        $this->transform($value)->shouldReturn(6);
    }
}
