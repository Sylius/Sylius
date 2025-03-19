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
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Resolver\UriTemplateParentResourceResolverInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
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
        UnitOfWork $unitOfWork,
        EntityPersister $entityPersister,
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

        $repository = new EntityRepository($entityManager->getWrappedObject(), new ClassMetadata($parentItem::class));

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $entityManager->getRepository($parentItem::class)->willReturn($repository);

        $unitOfWork->getEntityPersister($parentItem::class)->willReturn($entityPersister);
        $entityPersister->load(['code' => 'value'], null, null, [], null, 1, null)->willReturn(null);

        $this
            ->shouldThrow(NotFoundHttpException::class)
            ->during('resolve', [$item, $operation, ['uri_variables' => ['variable' => 'value']], []]);
    }

    function it_resolves_parent_resource(
        EntityManagerInterface $entityManager,
        ResourceInterface $item,
        ResourceInterface $parentItem,
        UnitOfWork $unitOfWork,
        EntityPersister $entityPersister,
    ): void {
        $repository = new EntityRepository($entityManager->getWrappedObject(), new ClassMetadata($parentItem::class));

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $entityManager->getRepository($parentItem::class)->willReturn($repository);

        $unitOfWork->getEntityPersister($parentItem::class)->willReturn($entityPersister);
        $entityPersister->load(['code' => 'value'], null, null, [], null, 1, null)->willReturn($parentItem);

        $operation = new Post(uriVariables: [
            'variable' => new Link(parameterName: 'variable', fromClass: $parentItem::class),
        ]);

        $this->resolve($item, $operation, ['uri_variables' => ['variable' => 'value']])->shouldReturn($parentItem);
    }
}
