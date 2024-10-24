<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Resolver;

use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Resolver\UriTemplateParentResourceResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UriTemplateParentResourceResolverSpec extends ObjectBehavior
{
    function let(EntityManagerInterface $entityManager): void
    {
        $this->beConstructedWith($entityManager);
    }

    function it_implies_uri_template_parent_resource_resolver_interface(): void
    {
        $this->shouldImplement(UriTemplateParentResourceResolverInterface::class);
    }

    function it_throws_an_exception_if_no_uri_variables_are_passed(
        EntityManagerInterface $entityManager,
        ResourceInterface $item,
    ): void {
        $entityManager->getRepository(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\RuntimeException::class)
            ->during('resolve', [$item, new Post(), [], []]);
    }

    function it_throws_an_exception_if_any_uri_variable_does_not_match(
        EntityManagerInterface $entityManager,
        ResourceInterface $item,
        ResourceInterface $parentItem,
    ): void {
        $entityManager->getRepository(Argument::any())->shouldNotBeCalled();

        $operation = new Post(uriVariables: [
            'variable' => new Link(fromClass: get_class($parentItem->getWrappedObject())),
        ]);

        $this
            ->shouldThrow(\RuntimeException::class)
            ->during('resolve', [$item, $operation, ['uri_variables' => ['variable' => 'value']], []]);
    }

    function it_throws_an_exception_if_uri_variable_class_is_not_defined(
        EntityManagerInterface $entityManager,
        ResourceInterface $item,
    ): void {
        $entityManager->getRepository(Argument::any())->shouldNotBeCalled();

        $operation = new Post(uriVariables: [
            'variable' => new Link(),
        ]);

        $this
            ->shouldThrow(\RuntimeException::class)
            ->during('resolve', [$item, $operation, ['uri_variables' => ['variable' => 'value']], []]);
    }

    function it_throws_an_exception_if_parent_resource_is_not_found(
        EntityManagerInterface $entityManager,
        ResourceInterface $item,
        RepositoryInterface $repository,
    ): void {
        $parentItem = new class() implements ResourceInterface {
            public function getId()
            {
                return null;
            }
        };

        $operation = new Post(uriVariables: [
            'variable' => new Link(parameterName: 'variable', fromClass: get_class($parentItem)),
        ]);

        $entityManager->getRepository(get_class($parentItem))->willReturn($repository);
        $repository->findOneBy(['code' => 'value'])->willReturn(null);

        $this
            ->shouldThrow(NotFoundHttpException::class)
            ->during('resolve', [$item, $operation, ['uri_variables' => ['variable' => 'value']], []]);
    }

    function it_resolves_parent_resource(
        EntityManagerInterface $entityManager,
        ResourceInterface $item,
        ResourceInterface $parentItem,
        RepositoryInterface $repository,
    ): void {
        $operation = new Post(uriVariables: [
            'variable' => new Link(parameterName: 'variable', fromClass: get_class($parentItem)),
        ]);

        $entityManager->getRepository(get_class($parentItem))->willReturn($repository);
        $repository->findOneBy(['code' => 'value'])->willReturn($parentItem);

        $this->resolve($item, $operation, ['uri_variables' => ['variable' => 'value']])->shouldReturn($parentItem);
    }
}
