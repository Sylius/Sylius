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

namespace Tests\Sylius\Bundle\UiBundle\Twig\Component;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class ResourceFormComponentTraitTest extends TestCase
{
    public function testItCreatesResourceUsingConfiguredFactory(): void
    {
        $resource = $this->createMock(ResourceInterface::class);

        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())->method('createNew')->willReturn($resource);

        $component = $this->createComponent(factory: $factory);

        $this->assertSame($resource, $component->createResource());
    }

    public function testItFallsBackToDirectInstantiationWhenNoFactoryIsConfigured(): void
    {
        $prototype = new class() implements ResourceInterface {
            public function getId()
            {
                return null;
            }
        };

        $component = $this->createComponent(resourceClass: $prototype::class);

        $created = $component->createResource();

        $this->assertInstanceOf($prototype::class, $created);
        $this->assertNotSame($prototype, $created);
    }

    public function testItHydratesEmptyValueByCreatingNewResource(): void
    {
        $resource = $this->createMock(ResourceInterface::class);

        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())->method('createNew')->willReturn($resource);

        $component = $this->createComponent(factory: $factory);

        $this->assertSame($resource, $component->hydrateResource(null));
    }

    public function testItHydratesNonEmptyValueByFindingResourceInRepository(): void
    {
        $resource = $this->createMock(ResourceInterface::class);

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->expects($this->once())->method('find')->with(5)->willReturn($resource);

        $component = $this->createComponent(repository: $repository);

        $this->assertSame($resource, $component->hydrateResource(5));
    }

    public function testItDehydratesResourceToItsId(): void
    {
        $resource = $this->createMock(ResourceInterface::class);
        $resource->method('getId')->willReturn(7);

        $component = $this->createComponent();

        $this->assertSame(7, $component->dehydrateResource($resource));
    }

    public function testItDehydratesNullResourceToNull(): void
    {
        $component = $this->createComponent();

        $this->assertNull($component->dehydrateResource(null));
    }

    public function testItInstantiatesFormForTheCurrentResource(): void
    {
        $resource = $this->createMock(ResourceInterface::class);
        $form = $this->createMock(FormInterface::class);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())
            ->method('create')
            ->with('form_class', $resource)
            ->willReturn($form);

        $component = $this->createComponent(formFactory: $formFactory);
        $component->resource = $resource;

        $this->assertSame($form, $component->instantiateForm());
    }

    private function createComponent(
        ?FactoryInterface $factory = null,
        ?RepositoryInterface $repository = null,
        ?FormFactoryInterface $formFactory = null,
        string $resourceClass = ResourceInterface::class,
        string $formClass = 'form_class',
    ): object {
        return new class($repository ?? $this->createMock(RepositoryInterface::class), $formFactory ?? $this->createMock(FormFactoryInterface::class), $resourceClass, $formClass, $factory, ) {
            use ResourceFormComponentTrait {
                initialize as public __construct;
                createResource as public;
                instantiateForm as public;
            }
        };
    }
}
